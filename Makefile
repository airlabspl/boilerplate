.PHONY: watch
watch:
	go tool air \
		-build.full_bin="./tmp/main" \
		-build.cmd="make build" \
		-build.include_ext="go,ts,tsx,sql,css" \
		-build.exclude_dir="web/node_modules" \
		-build.send_interrupt=true \
		-proxy.enabled=true \
		-proxy.proxy_port=3000 \
		-proxy.app_port=4000

.PHONY: build
build: ui
	go build -o ./tmp/main ./cmd/app/main.go

.PHONY: test
test: build
	go test ./...

.PHONY: ui
ui:
	npm run build --prefix ./web

.PHONY: clean
clean:
	rm -rf ./tmp
	rm -rf ./ui/dist
