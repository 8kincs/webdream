## Adatbázis feltöltése tesztadatokkal
```bash
php bin/console doctrine:fixtures:load
```

## Feltevések
- Minden raktárban mindegyik termék előfordulhat.
  - új raktár felvételekor mindegyik termék hozzáadódik az új raktárhoz
    - alapértelmezett raktárkapacitással -> 50
    - 0 készlettel

## Működési sajátosságok
- Ha a raktárkészlet módosítás csak részlegesen teljesíthető
  - a részleges teljesítés megtörténik 
  - a fennmaradt darabszám visszajelződik
  - több raktárat érintő tranzakció esetén 
    - nincs visszajelzés a részletekről
    - csak a végeredményről

## Tesztelés
- a tesztek előtt és sikeres tesztfutás után törlődnek a következő táblák
  - inStock
  - storage