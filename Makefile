.PHONY: dev
dev:
	composer run dev

.PHONY: test
test:
	php artisan test