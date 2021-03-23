const http = require('http');

const nStatic = require('node-static');

// Запуск должен производиться из корня проекта, чтобы Node.js правильно подхватил путь.
// node .\node-server\server.js
const fileServer = new nStatic.Server('progress-page');

http.createServer(function (request, response) {
    request.addListener('end', function () {
        //
        // Serve files!
        //
        fileServer.serve(request, response, function (err, result) {
            if (err) { // There was an error serving the file
                console.error("Error serving " + request.url + " - " + err.message);

                // Respond to the client
                response.writeHead(err.status, err.headers);
                response.end();
            }
        });
    }).resume();
}).listen(8500);
