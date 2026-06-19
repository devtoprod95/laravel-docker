# MyApp — Laravel Admin Starter (Tabler UI)

Laravel 13 기반의 **관리자 전용 페이지 데모 프로젝트**입니다.
프론트엔드는 [Tabler](https://tabler.io/) 템플릿을 Blade 뷰로 구성했으며, 새 프로젝트를 시작할 때 바로 이어서 작업할 수 있도록 관리자 페이지의 기본 레이아웃(헤더, 사이드 네비게이션, 인증 등)을 미리 구축해 둔 **초기 셋팅용 보일러플레이트**입니다.

새 프로젝트에 투입될 때 이 레포를 클론해서 도메인 로직만 얹으면 되는 것을 목표로 합니다.

---

## 주요 특징

- **Laravel 13** 기반, View는 전부 **Blade**로 작성
- **Tabler** 어드민 템플릿 적용 (헤더 / 사이드바 / 카드 / 테이블 등 공통 레이아웃 포함)
- 관리자 **로그인/인증 기능 포함**
- **Docker / Docker Compose**로 로컬 구동 가능 (PHP-FPM + Nginx + Redis)
- **MySQL / PostgreSQL 둘 다 지원** (드라이버 빌트인, `.env` 설정만 변경하면 전환 가능)
- 아이콘 라이브러리 등 관리자 화면에서 바로 활용 가능한 UI 리소스 포함

---

## 기술 스택

| 영역 | 사용 기술 |
|---|---|
| Backend | Laravel 13, PHP 8.3 (FPM) |
| View | Blade, Tabler UI |
| DB | MySQL 또는 PostgreSQL (선택) |
| Cache / Session | Redis |
| Web Server | Nginx (Alpine) |
| 실행 환경 | Docker, Docker Compose |

---

## 디렉터리 구조 (요약)

```
.
├── app/                        # Laravel 애플리케이션 코드
├── resources/views/            # Blade 뷰 (Tabler 레이아웃 포함)
├── docker/
│   ├── nginx/conf.d/default.conf
│   ├── php.ini
│   └── Dockerfile
├── docker-compose.yml
├── docker-entrypoint.sh
├── .env.example
└── README.md
```

---

## 요구 사항

- Docker / Docker Compose
- (선택) 로컬에 Git 설치

> PHP, Composer, Node.js 등은 모두 Docker 이미지 내부에 포함되어 있어 **로컬에 별도 설치할 필요가 없습니다.**

---

## 시작하기 (Quick Start)

### 1. 프로젝트 클론

```bash
git clone <repository-url> myapp
cd myapp
```

### 2. 환경 변수 설정

```bash
cp .env.example .env
```

`.env`에서 아래 항목을 환경에 맞게 수정합니다.

```env
APP_NAME=MyApp
APP_ENV=local
APP_URL=http://localhost:9083

# DB 드라이버 선택: mysql 또는 pgsql
DB_CONNECTION=mysql
DB_HOST=mysql        # 또는 postgres (사용하는 DB 서비스명에 맞게)
DB_PORT=3306         # mysql: 3306 / pgsql: 5432
DB_DATABASE=myapp
DB_USERNAME=myapp
DB_PASSWORD=secret

REDIS_HOST=phpredis
REDIS_PORT=6379

NGINX_PORT=9083
PHP_PORT=9000
```

> ⚠️ 본 `docker-compose.yml` 기본 구성에는 **DB 컨테이너가 포함되어 있지 않습니다.** 로컬 환경에 맞춰 MySQL 또는 PostgreSQL 컨테이너를 compose에 추가하거나, 외부(원격) DB에 연결해서 사용하세요. (예시는 [DB 컨테이너 추가하기](#db-컨테이너-추가하기-선택) 참고)

### 3. 컨테이너 빌드 & 실행

```bash
docker compose up -d --build
```

### 4. 컨테이너 진입 후 초기 셋팅

```bash
docker compose exec php83 bash

# 컨테이너 내부에서 실행
composer install
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
```

### 5. 접속 확인

브라우저에서 아래 주소로 접속합니다.

```
http://localhost:9083
```

관리자 로그인 페이지가 표시되면 정상 구동된 것입니다. 시드 데이터에 포함된 관리자 계정으로 로그인하세요. (계정 정보는 `database/seeders/` 참고)

---

## Docker 구성

### docker-compose.yml

| 서비스 | 설명 | 포트 |
|---|---|---|
| `nginx` | 웹 서버, Blade로 렌더링된 페이지 서빙 | `9083` (호스트) → `80` (컨테이너) |
| `php83` | PHP 8.3-FPM, Laravel 애플리케이션 실행 | `9000` (내부 expose) |
| `phpredis` | 세션/캐시용 Redis | `6379` |

```yaml
services:
  nginx:
    image: nginx:alpine
    container_name: nginx
    ports:
      - "${NGINX_PORT:-9083}:80"
    environment:
      - TZ=Asia/Seoul
    volumes:
      - ./docker/nginx/conf.d/default.conf:/etc/nginx/conf.d/default.conf
      - .:/var/www/html
    depends_on:
      - php83

  php83:
    build: ./docker
    container_name: php83
    restart: always
    user: "1000:1000"
    expose:
      - "${PHP_PORT:-9000}"
    environment:
      - TZ=Asia/Seoul
      - REDIS_HOST=phpredis
      - REDIS_PORT=${REDIS_PORT:-6379}
    volumes:
      - ./docker/php.ini:/usr/local/etc/php/php.ini
      - .:/var/www/html
    depends_on:
      - phpredis

  phpredis:
    image: redis:alpine
    container_name: phpredis
    restart: always
    ports:
      - "${REDIS_PORT:-6379}:6379"
    volumes:
      - redis_data:/data
    command: redis-server --appendonly yes

volumes:
  redis_data:
```

### PHP 컨테이너 (docker/Dockerfile)

- Base: `php:8.3-fpm`
- 확장 모듈: `pdo_mysql`, `pdo_pgsql`, `pgsql`, `mbstring`, `zip`, `exif`, `pcntl`, `bcmath`, `gd`, `redis`
- **MySQL / PostgreSQL 드라이버가 모두 설치되어 있어** `.env`의 `DB_CONNECTION` 값만 바꾸면 DB를 전환할 수 있습니다.
- Node.js 20.x 포함 (Tabler 에셋 빌드용)
- Composer 포함 (`composer:latest` 이미지에서 바이너리 복사)
- Timezone: `Asia/Seoul`
- 컨테이너 내부 `www-data` 사용자를 UID/GID `1000`으로 맞춰 호스트와 파일 권한 충돌 방지

```dockerfile
FROM php:8.3-fpm

ENV TZ=Asia/Seoul

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    libpq-dev \
    zip unzip \
    jpegoptim optipng pngquant gifsicle \
    vim git curl tzdata supervisor && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring zip exif pcntl bcmath gd && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && \
    echo "date.timezone = Asia/Seoul" > /usr/local/etc/php/conf.d/timezone.ini

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker-entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN passwd -d root
WORKDIR /var/www/html

RUN echo "umask 000" >> /root/.bashrc

RUN usermod -u 1000 www-data && \
    groupmod -g 1000 www-data && \
    chown -R www-data:www-data /var/www/html

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
```

### DB 컨테이너 추가하기 (선택)

기본 compose 파일에는 DB 서비스가 없으므로, 필요에 따라 아래 둘 중 하나를 `docker-compose.yml`의 `services` 항목에 추가하세요.

**MySQL 사용 시**

```yaml
  mysql:
    image: mysql:8.0
    container_name: mysql
    restart: always
    environment:
      - TZ=Asia/Seoul
      - MYSQL_DATABASE=myapp
      - MYSQL_USER=myapp
      - MYSQL_PASSWORD=secret
      - MYSQL_ROOT_PASSWORD=root
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
```

**PostgreSQL 사용 시**

```yaml
  postgres:
    image: postgres:16-alpine
    container_name: postgres
    restart: always
    environment:
      - TZ=Asia/Seoul
      - POSTGRES_DB=myapp
      - POSTGRES_USER=myapp
      - POSTGRES_PASSWORD=secret
    ports:
      - "5432:5432"
    volumes:
      - postgres_data:/var/lib/postgresql/data
```

추가한 DB 서비스명에 맞춰 `.env`의 `DB_HOST`, `DB_PORT`, `DB_CONNECTION`을 수정하고, 하단 `volumes:`에도 `mysql_data:` 또는 `postgres_data:`를 추가해주세요.

---

## 관리자 페이지 구성

현재 포함된 기본 레이아웃 / 기능:

- **로그인 / 인증**: 관리자 로그인 화면 및 인증 미들웨어 적용
- **공통 레이아웃**: 상단 헤더(앱 이름, 관리자 정보) + 좌측 사이드 네비게이션(홈 / 대시보드 / 관리자 / 설정)
- **아이콘 라이브러리 페이지**: Tabler Icons(`ti ti-*`) 204개를 카테고리별(핵심 작업 CRUD, 사용자 및 관리, 시스템 및 설정, 콘텐츠 및 미디어, 통계 및 리포트, 커뮤니케이션, 위치 및 이동, 기타)로 모아 보여주고, 클릭 시 클래스명이 복사되는 내부 도구 페이지 포함 — 화면 개발 시 아이콘을 빠르게 찾고 적용할 수 있도록 도와줍니다.

새로운 화면을 추가할 때는 이 공통 레이아웃(`resources/views/layouts/`)을 `@extends` 하여 콘텐츠 영역만 채우면 됩니다.

---

## 자주 쓰는 명령어

```bash
# 컨테이너 로그 확인
docker compose logs -f php83

# 컨테이너 진입
docker compose exec php83 bash

# 마이그레이션
docker compose exec php83 php artisan migrate

# 캐시/설정 초기화
docker compose exec php83 php artisan optimize:clear

# 프론트엔드 에셋 빌드 (watch)
docker compose exec php83 npm run dev

# 컨테이너 중지
docker compose down
```

---

## 트러블슈팅

| 증상 | 확인 사항 |
|---|---|
| 파일 권한 오류 (Permission denied) | Dockerfile에서 `www-data`를 UID/GID `1000`으로 맞췄으므로, 호스트 사용자도 UID `1000`인지 확인 |
| DB 연결 실패 | `.env`의 `DB_CONNECTION`, `DB_HOST`, `DB_PORT`가 실제 추가한 DB 컨테이너 서비스명/포트와 일치하는지 확인 |
| 포트 충돌 | `.env`의 `NGINX_PORT`, `REDIS_PORT` 값을 다른 포트로 변경 후 `docker compose up -d` 재실행 |
| Redis 연결 실패 | `phpredis` 컨테이너가 정상 기동했는지 `docker compose ps`로 확인 |

---

## 라이선스

내부/데모 목적의 보일러플레이트 프로젝트입니다. (라이선스 정책에 맞게 본 섹션을 수정하세요)

## 샘플 화면
