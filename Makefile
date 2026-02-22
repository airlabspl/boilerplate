.PHONY: dev
dev:
	go run .

.PHONY: test
test:
	go test ./...
