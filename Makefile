no-command:
	@echo Usage: make [scenario]


# Запускаем веб-сервер на порт 8500, который раздаёт статический контент из папки /docs
raw-server:
	node ./node-server/server.js

# Запуск сервера в режиме демона в менеджере процессов PM2, что делает его очень удобным в управлении.
server:
	pm2 start ./node-server/server.js


# Запуск прокси с отслеживанием файлов, который перезагружает страницу при любом изменении файлов в отслеживаемой папке.
# См. https://www.npmjs.com/package/browser-sync
browser:
	browser-sync start \
		--proxy "http://localhost:8500/" \
		--files "./docs/js/*.js" \
		--files "./docs/css/*.css" \
		--files "./docs/index.html"
