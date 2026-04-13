# Filament Classroom Reservation

Aplikacja do zarzadzania rezerwacjami sal lekcyjnych, zbudowana na Laravel + Filament.

## Techstack

- **PHP** 8.4
- **Laravel** 12
- **Filament** 5
- **Tailwind CSS** 4
- **SQLite** (default)
- **Pest** 4 (testing)

## Klonowanie i instalacja

```bash
git clone https://github.com/zstio-pt/blank-filament-app.git
cd blank-filament-app

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan storage:link
php artisan boost:install

npm run build
```

## Uruchamianie projektu

```bash
composer run dev
```

## Eksport tygodnia do PDF

W aplikacji dostepna jest opcja eksportu planu tygodnia do pliku PDF.

Gdzie znajdziesz eksport:

- w kalendarzu oblozenia sal: przycisk `Export Weekly PDF`,
- na liscie rezerwacji: przycisk `Export This Week PDF`.

Jak to dziala:

- eksport obejmuje pelen tydzien (od poniedzialku do niedzieli),
- nazwa pliku ma format `weekly-schedule-YYYY-MM-DD.pdf` (data poczatku tygodnia),
- dla administratora eksport zawiera wszystkie rezerwacje,
- dla nauczyciela eksport zawiera tylko jego rezerwacje.


## Seedowanie danych

Aby wypelnic baze danymi demonstracyjnymi (admin, nauczyciele, budynki, sale, rezerwacje):

```bash
php artisan db:seed
```

### Dane logowania po seedowaniu

- admin: `admin@example.com` / `password`
- nauczyciele (przykladowi):
	- `alice.teacher@example.com` / `password`
	- `bob.teacher@example.com` / `password`
	- `celia.teacher@example.com` / `password`

## Jak emulowac konflikt rezerwacji

Seeder tworzy rezerwacje od poczatku biezacego tygodnia (`startOfWeek`) dla 3 kolejnych tygodni.

Przyklad konfliktu:

1. Otworz panel i sproboj utworzyc rezerwacje dla sali `Room 101`.
2. Ustaw date na poniedzialek tygodnia, w ktorym uruchomiono seeder.
3. Ustaw godziny `08:00` - `09:00`.

Ten termin jest juz zajety przez wpis `Room 101 Morning Session`, wiec powinienes otrzymac blad konfliktu.

## Testy

```bash
php artisan test --compact
```

## Formatowanie kodu

```bash
vendor/bin/pint
```

## Przydatne polecenia

```bash
php artisan make:filament-user
composer run dev
```
