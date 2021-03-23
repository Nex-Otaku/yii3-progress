const got = require('got');
const jsdom = require("jsdom");
const { JSDOM } = jsdom;

const url = 'https://www.yiiframework.com/status/3.0';

const isStableVersion = (version) => {
    if (version === '') {
        return false;
    }

    const parts = version.split('.');
    const firstPart = parts[0];
    const firstVersionNumber = parseInt(firstPart);

    if (isNaN(firstVersionNumber)) {
        return false;
    }

    return firstVersionNumber >= 1;
};

got(url).then(response => {
    const dom = new JSDOM(response.body);
    const trs = dom.window.document.querySelectorAll('#w0 > table > tbody > tr');
    const totalPackageCount = trs.length;
    let stablePackageCount = 0;

    for (let i = 0; i < trs.length; i++) {
        const tr = trs[i];
        const name = tr.querySelectorAll('td').item(0).querySelector('a').textContent;
        const latestVersion = tr.querySelectorAll('td').item(1).textContent.trim();
        const isStable = isStableVersion(latestVersion);
        console.log(tr, name, latestVersion, isStable);

        if (isStable) {
            stablePackageCount++;
        }
    }

    const progress = Math.floor((100 * stablePackageCount) / totalPackageCount);
    console.log('Всего пакетов: ' + totalPackageCount + ', релизнуто: ' + stablePackageCount + ', процент готовности: ' + progress + '%');
}).catch(err => {
    console.log(err);
});