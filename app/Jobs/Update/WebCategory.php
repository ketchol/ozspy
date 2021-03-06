<?php

namespace OzSpy\Jobs\Update;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Exceptions\Crawl\CategoriesNotFoundException;
use OzSpy\Jobs\Models\WebCategory\UpdateOrStore;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory as WebCategoryModel;

class WebCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $retailer;

    protected $storedCategories = [];

    /**
     * @var WebCategoryContract
     */
    protected $categoryRepo;

    /**
     * @var WebCategoryModel
     */
    protected $categoryModel;

    /**
     * Create a new job instance.
     *
     * @param Retailer $retailer
     */
    public function __construct(Retailer $retailer)
    {
        $this->retailer = $retailer;
    }

    /**
     * Execute the job.
     * @param WebCategoryContract $categoryRepo
     * @param WebCategoryModel $categoryModel
     * @return void
     * @throws CategoriesNotFoundException
     */
    public function handle(WebCategoryContract $categoryRepo, WebCategoryModel $categoryModel)
    {
        $this->categoryRepo = $categoryRepo;

        $this->categoryModel = $categoryModel;

        $filePath = storage_path('app/scraper/storage/categories/' . $this->retailer->getKey() . '.json');
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $scrapingResult = json_decode($content);
            if (!is_null($scrapingResult) && json_last_error() === JSON_ERROR_NONE) {
                if (isset($scrapingResult->retailer_id) && isset($scrapingResult->scraped_at) && isset($scrapingResult->categories)) {
                    $retailer_id = $scrapingResult->retailer_id;
                    $categories = $scrapingResult->categories;
                    $last_scraped_at = Carbon::parse($scrapingResult->scraped_at);
                    if ($this->retailer->getKey() == $retailer_id) {

                        if (count($categories) == 0) {
                            throw new CategoriesNotFoundException;
                        }

                        foreach ($categories as $category) {
                            $this->processSingleCategory($category);
                        }
                        $this->retailer = $this->retailer->fresh();
                        $this->retailer->last_crawled_at = $last_scraped_at;
                        $this->retailer->save();
                    }
                }
            }
        }
    }

    /**
     * process and store a single category
     * @param $category
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|WebCategoryModel
     */
    protected function processSingleCategory($category)
    {
        $categoryData = (array)$category;

        if (array_get($categoryData, 'active') !== false) {
            $categoryData = array_except($categoryData, 'active');
        }

        dispatch((new UpdateOrStore($this->retailer, $categoryData)));
    }

    /**
     * restore the deleted categories being found in page
     * @return void
     */
    protected function restoreCategories()
    {
        foreach ($this->storedCategories as $storedCategory) {
            if ($storedCategory->trashed()) {
                $this->categoryRepo->restore($storedCategory);
            }
        }
    }

    /**
     * delete the categories not found in page
     * @return void
     */
    protected function deleteCategories()
    {
        $categories = $this->retailer->webCategories;
        $notFoundCategories = $categories->diff($this->storedCategories);
        $notFoundCategoryIds = $notFoundCategories->pluck('id');
        $this->retailer->webCategories()->whereIn('id', $notFoundCategoryIds)->delete();
    }

    /**
     * filter provided data
     * @param array $data
     * @return array
     */
    private function __getData(array $data)
    {
        return array_only($data, $this->categoryModel->getFillable());
    }

    /**
     * add category object to array
     * @param WebCategoryModel $category
     * @return void
     */
    private function __signCategory(WebCategoryModel $category)
    {
        array_push($this->storedCategories, $category);
    }
}
