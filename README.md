# backmarket-price-scrapper

## Requirements

- PHP 7.1
- Composer

## Installation

```
cd backmarket-price-scrapper/
composer install
```

## Usage

Execute a scrapper: `bin/console`


```
./bin/console backmarket:price:downloader
| Name                  | Grade    | Price |
+-----------------------+----------+-------+
| iPhone 6S 64Go Gris   | Bronze   | 359   |
| iPhone 6S 64Go Gris   | Argent   | 373   |
| iPhone 6S 64Go Argent | Stallone | 384   |
| iPhone 6S 64Go Gris   | Or       | 388   |
| iPhone 6S 64Go Argent | Bronze   | 389   |
| iPhone 6S 64Go Argent | Argent   | 394   |
| iPhone 6S 32Go Gris   | Argent   | 399   |
| iPhone 6S 32Go Argent | Argent   | 402   |
| iPhone 6S 64Go Gris   | Shiny    | 417   |
| iPhone 6S 32Go Gris   | Stallone | 419   |
| iPhone 6S 64Go Argent | Or       | 423   |
| iPhone 6S 32Go Gris   | Bronze   | 424   |
| iPhone 6S 64Go Argent | Shiny    | 424   |
| iPhone 6S 32Go Gris   | Or       | 439   |
| iPhone 6S 32Go Argent | Or       | 449   |
| iPhone 6S 32Go Gris   | Shiny    | 468   |
+-----------------------+----------+-------+

```

## Change product list

Fow now edit ProductPriceDownloaderCommand



* Note: If your are a representative of backmarket and you disapprove of this just drop me an email.