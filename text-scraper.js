var page = require('webpage').create();
var system = require('system');
var fs = require('fs');

if(system.args.length !== 3) {
  console.log('Usage: phantomjs text-scraper.js <url> <output file>');
  phantom.exit();
}

var url = system.args[1];
var outfile = system.args[2];

page.onConsoleMessage = function(msg) {
  console.log(msg);
};

page.open(url, function(status) {
  var output = url + '\n';
  if(status === 'success') {
    setTimeout(function() {
      var text = page.evaluate(function () {
		var text1= document.title + '\n' + document.body.innerText+'\n';
		var text2 = '';
		var l = document.getElementsByTagName('a');
		for(var i = 0; i<l.length; i++) {
			text2+= l[i].href+'\n';
		}
		return text1+text2;
//        return document.title + '\n' + document.body.innerText;
      });
      output += text;
      fs.write(outfile, output);
    }, 1000);
    setTimeout(function () {
      phantom.exit()
    }, 1000);
  } else {
    console.log("Error!")
    phantom.exit()
  }
});
