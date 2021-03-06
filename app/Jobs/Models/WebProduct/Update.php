<?php

namespace OzSpy\Jobs\Models\WebProduct;

use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Models\Base\WebProduct;

class Update implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebProduct
     */
    protected $webProduct;

    /**
     * @var array
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param WebProduct $webProduct
     * @param array $data
     */
    public function __construct(WebProduct $webProduct, array $data)
    {
        $this->webProduct = $webProduct;

        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->webProduct->update($this->__getData($this->data));
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webProduct->getFillable());
    }
}
