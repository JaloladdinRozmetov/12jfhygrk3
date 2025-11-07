env:
	@if [ -f .env ]; then \
		echo "✅ .env already exists."; \
	else \
		if [ -f .env.example ]; then \
			cp .env.example .env; \
			echo "⚙️  .env created from .env.example"; \
		else \
			echo "❌ .env.example not found. Please create one."; \
		fi; \
	fi
up: env
	docker compose up -d
composer-install:
	docker exec story_valut composer update --no-interaction --quiet
migrate:
	docker exec story_valut php yii migrate --interactive=0
start: up composer-install
	@echo "✅ Environment ready, Docker running, Composer installed."
down:
	docker compose down
