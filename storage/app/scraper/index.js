let argvs = {};

process.argv.forEach((argv) => {
    if (argv.indexOf('=') > -1) {
        let element = argv.split(/=(.+)/);
        if (element.length >= 2) {
            let key = element[0].replace('--', '');
            argvs[key] = element[1];
        }
    }
});

if (!argvs.scraper) {
    argvs.scraper = 'categories';
}

if (argvs.product) {
    argvs.product = JSON.parse(decodeURIComponent(argvs.product));
}

if (argvs.category) {
    argvs.category = JSON.parse(decodeURIComponent(argvs.category));
}

if (!argvs.retailer) {
    throw new Error('Retailer not found.');
}

if (argvs.test) {
    argvs.test = argvs.test === 'true' || argvs.test === '1';
} else {
    argvs.test = false;
}

argvs.retailer = JSON.parse(decodeURIComponent(argvs.retailer));

let Scraper = require('./scrapers/' + argvs.retailer.abbreviation + '/' + argvs.scraper);

let scraper = new Scraper(argvs);
scraper.scrape();