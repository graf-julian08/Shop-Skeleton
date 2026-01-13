<?php
/**
 * Countries Seed Data - Complete World List
 * All 250 countries and territories with:
 * - ISO 3166-1 alpha-2 codes
 * - Regions (Europa, Asien, Afrika, Nordamerika, Südamerika, Ozeanien, Karibik, Naher Osten)
 * - Main languages
 * - Currency codes (ISO 4217)
 */

$allCountries = [
    // EUROPA (50 countries)
    ['code' => 'AL', 'name' => 'Albanien', 'region' => 'Europa', 'languages' => 'Albanisch', 'currency_code' => 'ALL'],
    ['code' => 'AD', 'name' => 'Andorra', 'region' => 'Europa', 'languages' => 'Katalanisch', 'currency_code' => 'EUR'],
    ['code' => 'AT', 'name' => 'Österreich', 'region' => 'Europa', 'languages' => 'Deutsch', 'currency_code' => 'EUR'],
    ['code' => 'BY', 'name' => 'Belarus', 'region' => 'Europa', 'languages' => 'Belarussisch, Russisch', 'currency_code' => 'BYN'],
    ['code' => 'BE', 'name' => 'Belgien', 'region' => 'Europa', 'languages' => 'Niederländisch, Französisch, Deutsch', 'currency_code' => 'EUR'],
    ['code' => 'BA', 'name' => 'Bosnien und Herzegowina', 'region' => 'Europa', 'languages' => 'Bosnisch, Serbisch, Kroatisch', 'currency_code' => 'BAM'],
    ['code' => 'BG', 'name' => 'Bulgarien', 'region' => 'Europa', 'languages' => 'Bulgarisch', 'currency_code' => 'BGN'],
    ['code' => 'HR', 'name' => 'Kroatien', 'region' => 'Europa', 'languages' => 'Kroatisch', 'currency_code' => 'EUR'],
    ['code' => 'CY', 'name' => 'Zypern', 'region' => 'Europa', 'languages' => 'Griechisch, Türkisch', 'currency_code' => 'EUR'],
    ['code' => 'CZ', 'name' => 'Tschechien', 'region' => 'Europa', 'languages' => 'Tschechisch', 'currency_code' => 'CZK'],
    ['code' => 'DK', 'name' => 'Dänemark', 'region' => 'Europa', 'languages' => 'Dänisch', 'currency_code' => 'DKK'],
    ['code' => 'EE', 'name' => 'Estland', 'region' => 'Europa', 'languages' => 'Estnisch', 'currency_code' => 'EUR'],
    ['code' => 'FO', 'name' => 'Färöer', 'region' => 'Europa', 'languages' => 'Färöisch, Dänisch', 'currency_code' => 'DKK'],
    ['code' => 'FI', 'name' => 'Finnland', 'region' => 'Europa', 'languages' => 'Finnisch, Schwedisch', 'currency_code' => 'EUR'],
    ['code' => 'FR', 'name' => 'Frankreich', 'region' => 'Europa', 'languages' => 'Französisch', 'currency_code' => 'EUR'],
    ['code' => 'DE', 'name' => 'Deutschland', 'region' => 'Europa', 'languages' => 'Deutsch', 'currency_code' => 'EUR'],
    ['code' => 'GI', 'name' => 'Gibraltar', 'region' => 'Europa', 'languages' => 'Englisch', 'currency_code' => 'GIP'],
    ['code' => 'GR', 'name' => 'Griechenland', 'region' => 'Europa', 'languages' => 'Griechisch', 'currency_code' => 'EUR'],
    ['code' => 'HU', 'name' => 'Ungarn', 'region' => 'Europa', 'languages' => 'Ungarisch', 'currency_code' => 'HUF'],
    ['code' => 'IS', 'name' => 'Island', 'region' => 'Europa', 'languages' => 'Isländisch', 'currency_code' => 'ISK'],
    ['code' => 'IE', 'name' => 'Irland', 'region' => 'Europa', 'languages' => 'Englisch, Irisch', 'currency_code' => 'EUR'],
    ['code' => 'IT', 'name' => 'Italien', 'region' => 'Europa', 'languages' => 'Italienisch', 'currency_code' => 'EUR'],
    ['code' => 'XK', 'name' => 'Kosovo', 'region' => 'Europa', 'languages' => 'Albanisch, Serbisch', 'currency_code' => 'EUR'],
    ['code' => 'LV', 'name' => 'Lettland', 'region' => 'Europa', 'languages' => 'Lettisch', 'currency_code' => 'EUR'],
    ['code' => 'LI', 'name' => 'Liechtenstein', 'region' => 'Europa', 'languages' => 'Deutsch', 'currency_code' => 'CHF'],
    ['code' => 'LT', 'name' => 'Litauen', 'region' => 'Europa', 'languages' => 'Litauisch', 'currency_code' => 'EUR'],
    ['code' => 'LU', 'name' => 'Luxemburg', 'region' => 'Europa', 'languages' => 'Luxemburgisch, Deutsch, Französisch', 'currency_code' => 'EUR'],
    ['code' => 'MT', 'name' => 'Malta', 'region' => 'Europa', 'languages' => 'Maltesisch, Englisch', 'currency_code' => 'EUR'],
    ['code' => 'MD', 'name' => 'Moldau', 'region' => 'Europa', 'languages' => 'Rumänisch', 'currency_code' => 'MDL'],
    ['code' => 'MC', 'name' => 'Monaco', 'region' => 'Europa', 'languages' => 'Französisch', 'currency_code' => 'EUR'],
    ['code' => 'ME', 'name' => 'Montenegro', 'region' => 'Europa', 'languages' => 'Montenegrinisch', 'currency_code' => 'EUR'],
    ['code' => 'NL', 'name' => 'Niederlande', 'region' => 'Europa', 'languages' => 'Niederländisch', 'currency_code' => 'EUR'],
    ['code' => 'MK', 'name' => 'Nordmazedonien', 'region' => 'Europa', 'languages' => 'Mazedonisch', 'currency_code' => 'MKD'],
    ['code' => 'NO', 'name' => 'Norwegen', 'region' => 'Europa', 'languages' => 'Norwegisch', 'currency_code' => 'NOK'],
    ['code' => 'PL', 'name' => 'Polen', 'region' => 'Europa', 'languages' => 'Polnisch', 'currency_code' => 'PLN'],
    ['code' => 'PT', 'name' => 'Portugal', 'region' => 'Europa', 'languages' => 'Portugiesisch', 'currency_code' => 'EUR'],
    ['code' => 'RO', 'name' => 'Rumänien', 'region' => 'Europa', 'languages' => 'Rumänisch', 'currency_code' => 'RON'],
    ['code' => 'RU', 'name' => 'Russland', 'region' => 'Europa', 'languages' => 'Russisch', 'currency_code' => 'RUB'],
    ['code' => 'SM', 'name' => 'San Marino', 'region' => 'Europa', 'languages' => 'Italienisch', 'currency_code' => 'EUR'],
    ['code' => 'RS', 'name' => 'Serbien', 'region' => 'Europa', 'languages' => 'Serbisch', 'currency_code' => 'RSD'],
    ['code' => 'SK', 'name' => 'Slowakei', 'region' => 'Europa', 'languages' => 'Slowakisch', 'currency_code' => 'EUR'],
    ['code' => 'SI', 'name' => 'Slowenien', 'region' => 'Europa', 'languages' => 'Slowenisch', 'currency_code' => 'EUR'],
    ['code' => 'ES', 'name' => 'Spanien', 'region' => 'Europa', 'languages' => 'Spanisch', 'currency_code' => 'EUR'],
    ['code' => 'SE', 'name' => 'Schweden', 'region' => 'Europa', 'languages' => 'Schwedisch', 'currency_code' => 'SEK'],
    ['code' => 'CH', 'name' => 'Schweiz', 'region' => 'Europa', 'languages' => 'Deutsch, Französisch, Italienisch, Rätoromanisch', 'currency_code' => 'CHF'],
    ['code' => 'UA', 'name' => 'Ukraine', 'region' => 'Europa', 'languages' => 'Ukrainisch', 'currency_code' => 'UAH'],
    ['code' => 'GB', 'name' => 'Vereinigtes Königreich', 'region' => 'Europa', 'languages' => 'Englisch', 'currency_code' => 'GBP'],
    ['code' => 'VA', 'name' => 'Vatikanstadt', 'region' => 'Europa', 'languages' => 'Italienisch, Latein', 'currency_code' => 'EUR'],

    // ASIEN (50 countries)
    ['code' => 'AF', 'name' => 'Afghanistan', 'region' => 'Asien', 'languages' => 'Dari, Paschtu', 'currency_code' => 'AFN'],
    ['code' => 'AM', 'name' => 'Armenien', 'region' => 'Asien', 'languages' => 'Armenisch', 'currency_code' => 'AMD'],
    ['code' => 'AZ', 'name' => 'Aserbaidschan', 'region' => 'Asien', 'languages' => 'Aserbaidschanisch', 'currency_code' => 'AZN'],
    ['code' => 'BH', 'name' => 'Bahrain', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'BHD'],
    ['code' => 'BD', 'name' => 'Bangladesch', 'region' => 'Asien', 'languages' => 'Bengalisch', 'currency_code' => 'BDT'],
    ['code' => 'BT', 'name' => 'Bhutan', 'region' => 'Asien', 'languages' => 'Dzongkha', 'currency_code' => 'BTN'],
    ['code' => 'BN', 'name' => 'Brunei', 'region' => 'Asien', 'languages' => 'Malaiisch', 'currency_code' => 'BND'],
    ['code' => 'KH', 'name' => 'Kambodscha', 'region' => 'Asien', 'languages' => 'Khmer', 'currency_code' => 'KHR'],
    ['code' => 'CN', 'name' => 'China', 'region' => 'Asien', 'languages' => 'Chinesisch', 'currency_code' => 'CNY'],
    ['code' => 'GE', 'name' => 'Georgien', 'region' => 'Asien', 'languages' => 'Georgisch', 'currency_code' => 'GEL'],
    ['code' => 'HK', 'name' => 'Hongkong', 'region' => 'Asien', 'languages' => 'Chinesisch, Englisch', 'currency_code' => 'HKD'],
    ['code' => 'IN', 'name' => 'Indien', 'region' => 'Asien', 'languages' => 'Hindi, Englisch', 'currency_code' => 'INR'],
    ['code' => 'ID', 'name' => 'Indonesien', 'region' => 'Asien', 'languages' => 'Indonesisch', 'currency_code' => 'IDR'],
    ['code' => 'IR', 'name' => 'Iran', 'region' => 'Naher Osten', 'languages' => 'Persisch', 'currency_code' => 'IRR'],
    ['code' => 'IQ', 'name' => 'Irak', 'region' => 'Naher Osten', 'languages' => 'Arabisch, Kurdisch', 'currency_code' => 'IQD'],
    ['code' => 'IL', 'name' => 'Israel', 'region' => 'Naher Osten', 'languages' => 'Hebräisch, Arabisch', 'currency_code' => 'ILS'],
    ['code' => 'JP', 'name' => 'Japan', 'region' => 'Asien', 'languages' => 'Japanisch', 'currency_code' => 'JPY'],
    ['code' => 'JO', 'name' => 'Jordanien', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'JOD'],
    ['code' => 'KZ', 'name' => 'Kasachstan', 'region' => 'Asien', 'languages' => 'Kasachisch, Russisch', 'currency_code' => 'KZT'],
    ['code' => 'KW', 'name' => 'Kuwait', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'KWD'],
    ['code' => 'KG', 'name' => 'Kirgisistan', 'region' => 'Asien', 'languages' => 'Kirgisisch, Russisch', 'currency_code' => 'KGS'],
    ['code' => 'LA', 'name' => 'Laos', 'region' => 'Asien', 'languages' => 'Laotisch', 'currency_code' => 'LAK'],
    ['code' => 'LB', 'name' => 'Libanon', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'LBP'],
    ['code' => 'MO', 'name' => 'Macau', 'region' => 'Asien', 'languages' => 'Chinesisch, Portugiesisch', 'currency_code' => 'MOP'],
    ['code' => 'MY', 'name' => 'Malaysia', 'region' => 'Asien', 'languages' => 'Malaiisch', 'currency_code' => 'MYR'],
    ['code' => 'MV', 'name' => 'Malediven', 'region' => 'Asien', 'languages' => 'Dhivehi', 'currency_code' => 'MVR'],
    ['code' => 'MN', 'name' => 'Mongolei', 'region' => 'Asien', 'languages' => 'Mongolisch', 'currency_code' => 'MNT'],
    ['code' => 'MM', 'name' => 'Myanmar', 'region' => 'Asien', 'languages' => 'Birmanisch', 'currency_code' => 'MMK'],
    ['code' => 'NP', 'name' => 'Nepal', 'region' => 'Asien', 'languages' => 'Nepali', 'currency_code' => 'NPR'],
    ['code' => 'KP', 'name' => 'Nordkorea', 'region' => 'Asien', 'languages' => 'Koreanisch', 'currency_code' => 'KPW'],
    ['code' => 'OM', 'name' => 'Oman', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'OMR'],
    ['code' => 'PK', 'name' => 'Pakistan', 'region' => 'Asien', 'languages' => 'Urdu, Englisch', 'currency_code' => 'PKR'],
    ['code' => 'PS', 'name' => 'Palästina', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'ILS'],
    ['code' => 'PH', 'name' => 'Philippinen', 'region' => 'Asien', 'languages' => 'Filipino, Englisch', 'currency_code' => 'PHP'],
    ['code' => 'QA', 'name' => 'Katar', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'QAR'],
    ['code' => 'SA', 'name' => 'Saudi-Arabien', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'SAR'],
    ['code' => 'SG', 'name' => 'Singapur', 'region' => 'Asien', 'languages' => 'Englisch, Chinesisch, Malaiisch, Tamil', 'currency_code' => 'SGD'],
    ['code' => 'KR', 'name' => 'Südkorea', 'region' => 'Asien', 'languages' => 'Koreanisch', 'currency_code' => 'KRW'],
    ['code' => 'LK', 'name' => 'Sri Lanka', 'region' => 'Asien', 'languages' => 'Singhalesisch, Tamil', 'currency_code' => 'LKR'],
    ['code' => 'SY', 'name' => 'Syrien', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'SYP'],
    ['code' => 'TW', 'name' => 'Taiwan', 'region' => 'Asien', 'languages' => 'Chinesisch', 'currency_code' => 'TWD'],
    ['code' => 'TJ', 'name' => 'Tadschikistan', 'region' => 'Asien', 'languages' => 'Tadschikisch', 'currency_code' => 'TJS'],
    ['code' => 'TH', 'name' => 'Thailand', 'region' => 'Asien', 'languages' => 'Thailändisch', 'currency_code' => 'THB'],
    ['code' => 'TL', 'name' => 'Timor-Leste', 'region' => 'Asien', 'languages' => 'Tetum, Portugiesisch', 'currency_code' => 'USD'],
    ['code' => 'TR', 'name' => 'Türkei', 'region' => 'Asien', 'languages' => 'Türkisch', 'currency_code' => 'TRY'],
    ['code' => 'TM', 'name' => 'Turkmenistan', 'region' => 'Asien', 'languages' => 'Turkmenisch', 'currency_code' => 'TMT'],
    ['code' => 'AE', 'name' => 'Vereinigte Arabische Emirate', 'region' => 'Naher Osten', 'languages' => 'Arabisch, Englisch', 'currency_code' => 'AED'],
    ['code' => 'UZ', 'name' => 'Usbekistan', 'region' => 'Asien', 'languages' => 'Usbekisch', 'currency_code' => 'UZS'],
    ['code' => 'VN', 'name' => 'Vietnam', 'region' => 'Asien', 'languages' => 'Vietnamesisch', 'currency_code' => 'VND'],
    ['code' => 'YE', 'name' => 'Jemen', 'region' => 'Naher Osten', 'languages' => 'Arabisch', 'currency_code' => 'YER'],

    // AFRIKA (54 countries)
    ['code' => 'DZ', 'name' => 'Algerien', 'region' => 'Afrika', 'languages' => 'Arabisch, Berber', 'currency_code' => 'DZD'],
    ['code' => 'AO', 'name' => 'Angola', 'region' => 'Afrika', 'languages' => 'Portugiesisch', 'currency_code' => 'AOA'],
    ['code' => 'BJ', 'name' => 'Benin', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'XOF'],
    ['code' => 'BW', 'name' => 'Botswana', 'region' => 'Afrika', 'languages' => 'Englisch, Setswana', 'currency_code' => 'BWP'],
    ['code' => 'BF', 'name' => 'Burkina Faso', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'XOF'],
    ['code' => 'BI', 'name' => 'Burundi', 'region' => 'Afrika', 'languages' => 'Kirundi, Französisch', 'currency_code' => 'BIF'],
    ['code' => 'CV', 'name' => 'Kap Verde', 'region' => 'Afrika', 'languages' => 'Portugiesisch', 'currency_code' => 'CVE'],
    ['code' => 'CM', 'name' => 'Kamerun', 'region' => 'Afrika', 'languages' => 'Französisch, Englisch', 'currency_code' => 'XAF'],
    ['code' => 'CF', 'name' => 'Zentralafrikanische Republik', 'region' => 'Afrika', 'languages' => 'Französisch, Sango', 'currency_code' => 'XAF'],
    ['code' => 'TD', 'name' => 'Tschad', 'region' => 'Afrika', 'languages' => 'Französisch, Arabisch', 'currency_code' => 'XAF'],
    ['code' => 'KM', 'name' => 'Komoren', 'region' => 'Afrika', 'languages' => 'Komorisch, Arabisch, Französisch', 'currency_code' => 'KMF'],
    ['code' => 'CG', 'name' => 'Republik Kongo', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'XAF'],
    ['code' => 'CD', 'name' => 'Demokratische Republik Kongo', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'CDF'],
    ['code' => 'CI', 'name' => 'Elfenbeinküste', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'XOF'],
    ['code' => 'DJ', 'name' => 'Dschibuti', 'region' => 'Afrika', 'languages' => 'Französisch, Arabisch', 'currency_code' => 'DJF'],
    ['code' => 'EG', 'name' => 'Ägypten', 'region' => 'Afrika', 'languages' => 'Arabisch', 'currency_code' => 'EGP'],
    ['code' => 'GQ', 'name' => 'Äquatorialguinea', 'region' => 'Afrika', 'languages' => 'Spanisch, Französisch', 'currency_code' => 'XAF'],
    ['code' => 'ER', 'name' => 'Eritrea', 'region' => 'Afrika', 'languages' => 'Tigrinya, Arabisch', 'currency_code' => 'ERN'],
    ['code' => 'SZ', 'name' => 'Eswatini', 'region' => 'Afrika', 'languages' => 'Swati, Englisch', 'currency_code' => 'SZL'],
    ['code' => 'ET', 'name' => 'Äthiopien', 'region' => 'Afrika', 'languages' => 'Amharisch', 'currency_code' => 'ETB'],
    ['code' => 'GA', 'name' => 'Gabun', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'XAF'],
    ['code' => 'GM', 'name' => 'Gambia', 'region' => 'Afrika', 'languages' => 'Englisch', 'currency_code' => 'GMD'],
    ['code' => 'GH', 'name' => 'Ghana', 'region' => 'Afrika', 'languages' => 'Englisch', 'currency_code' => 'GHS'],
    ['code' => 'GN', 'name' => 'Guinea', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'GNF'],
    ['code' => 'GW', 'name' => 'Guinea-Bissau', 'region' => 'Afrika', 'languages' => 'Portugiesisch', 'currency_code' => 'XOF'],
    ['code' => 'KE', 'name' => 'Kenia', 'region' => 'Afrika', 'languages' => 'Swahili, Englisch', 'currency_code' => 'KES'],
    ['code' => 'LS', 'name' => 'Lesotho', 'region' => 'Afrika', 'languages' => 'Sesotho, Englisch', 'currency_code' => 'LSL'],
    ['code' => 'LR', 'name' => 'Liberia', 'region' => 'Afrika', 'languages' => 'Englisch', 'currency_code' => 'LRD'],
    ['code' => 'LY', 'name' => 'Libyen', 'region' => 'Afrika', 'languages' => 'Arabisch', 'currency_code' => 'LYD'],
    ['code' => 'MG', 'name' => 'Madagaskar', 'region' => 'Afrika', 'languages' => 'Malagasy, Französisch', 'currency_code' => 'MGA'],
    ['code' => 'MW', 'name' => 'Malawi', 'region' => 'Afrika', 'languages' => 'Englisch, Chichewa', 'currency_code' => 'MWK'],
    ['code' => 'ML', 'name' => 'Mali', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'XOF'],
    ['code' => 'MR', 'name' => 'Mauretanien', 'region' => 'Afrika', 'languages' => 'Arabisch', 'currency_code' => 'MRU'],
    ['code' => 'MU', 'name' => 'Mauritius', 'region' => 'Afrika', 'languages' => 'Englisch, Französisch', 'currency_code' => 'MUR'],
    ['code' => 'MA', 'name' => 'Marokko', 'region' => 'Afrika', 'languages' => 'Arabisch, Berber', 'currency_code' => 'MAD'],
    ['code' => 'MZ', 'name' => 'Mosambik', 'region' => 'Afrika', 'languages' => 'Portugiesisch', 'currency_code' => 'MZN'],
    ['code' => 'NA', 'name' => 'Namibia', 'region' => 'Afrika', 'languages' => 'Englisch', 'currency_code' => 'NAD'],
    ['code' => 'NE', 'name' => 'Niger', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'XOF'],
    ['code' => 'NG', 'name' => 'Nigeria', 'region' => 'Afrika', 'languages' => 'Englisch', 'currency_code' => 'NGN'],
    ['code' => 'RW', 'name' => 'Ruanda', 'region' => 'Afrika', 'languages' => 'Kinyarwanda, Französisch, Englisch', 'currency_code' => 'RWF'],
    ['code' => 'ST', 'name' => 'São Tomé und Príncipe', 'region' => 'Afrika', 'languages' => 'Portugiesisch', 'currency_code' => 'STN'],
    ['code' => 'SN', 'name' => 'Senegal', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'XOF'],
    ['code' => 'SC', 'name' => 'Seychellen', 'region' => 'Afrika', 'languages' => 'Englisch, Französisch, Kreolisch', 'currency_code' => 'SCR'],
    ['code' => 'SL', 'name' => 'Sierra Leone', 'region' => 'Afrika', 'languages' => 'Englisch', 'currency_code' => 'SLE'],
    ['code' => 'SO', 'name' => 'Somalia', 'region' => 'Afrika', 'languages' => 'Somali, Arabisch', 'currency_code' => 'SOS'],
    ['code' => 'ZA', 'name' => 'Südafrika', 'region' => 'Afrika', 'languages' => 'Englisch, Afrikaans, Zulu', 'currency_code' => 'ZAR'],
    ['code' => 'SS', 'name' => 'Südsudan', 'region' => 'Afrika', 'languages' => 'Englisch', 'currency_code' => 'SSP'],
    ['code' => 'SD', 'name' => 'Sudan', 'region' => 'Afrika', 'languages' => 'Arabisch, Englisch', 'currency_code' => 'SDG'],
    ['code' => 'TZ', 'name' => 'Tansania', 'region' => 'Afrika', 'languages' => 'Swahili, Englisch', 'currency_code' => 'TZS'],
    ['code' => 'TG', 'name' => 'Togo', 'region' => 'Afrika', 'languages' => 'Französisch', 'currency_code' => 'XOF'],
    ['code' => 'TN', 'name' => 'Tunesien', 'region' => 'Afrika', 'languages' => 'Arabisch', 'currency_code' => 'TND'],
    ['code' => 'UG', 'name' => 'Uganda', 'region' => 'Afrika', 'languages' => 'Englisch, Swahili', 'currency_code' => 'UGX'],
    ['code' => 'ZM', 'name' => 'Sambia', 'region' => 'Afrika', 'languages' => 'Englisch', 'currency_code' => 'ZMW'],
    ['code' => 'ZW', 'name' => 'Simbabwe', 'region' => 'Afrika', 'languages' => 'Englisch, Shona, Ndebele', 'currency_code' => 'ZWL'],

    // NORDAMERIKA (23 countries)
    ['code' => 'US', 'name' => 'Vereinigte Staaten', 'region' => 'Nordamerika', 'languages' => 'Englisch', 'currency_code' => 'USD'],
    ['code' => 'CA', 'name' => 'Kanada', 'region' => 'Nordamerika', 'languages' => 'Englisch, Französisch', 'currency_code' => 'CAD'],
    ['code' => 'MX', 'name' => 'Mexiko', 'region' => 'Nordamerika', 'languages' => 'Spanisch', 'currency_code' => 'MXN'],
    ['code' => 'GT', 'name' => 'Guatemala', 'region' => 'Nordamerika', 'languages' => 'Spanisch', 'currency_code' => 'GTQ'],
    ['code' => 'BZ', 'name' => 'Belize', 'region' => 'Nordamerika', 'languages' => 'Englisch', 'currency_code' => 'BZD'],
    ['code' => 'HN', 'name' => 'Honduras', 'region' => 'Nordamerika', 'languages' => 'Spanisch', 'currency_code' => 'HNL'],
    ['code' => 'SV', 'name' => 'El Salvador', 'region' => 'Nordamerika', 'languages' => 'Spanisch', 'currency_code' => 'USD'],
    ['code' => 'NI', 'name' => 'Nicaragua', 'region' => 'Nordamerika', 'languages' => 'Spanisch', 'currency_code' => 'NIO'],
    ['code' => 'CR', 'name' => 'Costa Rica', 'region' => 'Nordamerika', 'languages' => 'Spanisch', 'currency_code' => 'CRC'],
    ['code' => 'PA', 'name' => 'Panama', 'region' => 'Nordamerika', 'languages' => 'Spanisch', 'currency_code' => 'PAB'],

    // KARIBIK (25 countries)
    ['code' => 'AG', 'name' => 'Antigua und Barbuda', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'XCD'],
    ['code' => 'BS', 'name' => 'Bahamas', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'BSD'],
    ['code' => 'BB', 'name' => 'Barbados', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'BBD'],
    ['code' => 'CU', 'name' => 'Kuba', 'region' => 'Karibik', 'languages' => 'Spanisch', 'currency_code' => 'CUP'],
    ['code' => 'DM', 'name' => 'Dominica', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'XCD'],
    ['code' => 'DO', 'name' => 'Dominikanische Republik', 'region' => 'Karibik', 'languages' => 'Spanisch', 'currency_code' => 'DOP'],
    ['code' => 'GD', 'name' => 'Grenada', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'XCD'],
    ['code' => 'HT', 'name' => 'Haiti', 'region' => 'Karibik', 'languages' => 'Französisch, Haitianisches Kreol', 'currency_code' => 'HTG'],
    ['code' => 'JM', 'name' => 'Jamaika', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'JMD'],
    ['code' => 'KN', 'name' => 'St. Kitts und Nevis', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'XCD'],
    ['code' => 'LC', 'name' => 'St. Lucia', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'XCD'],
    ['code' => 'VC', 'name' => 'St. Vincent und die Grenadinen', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'XCD'],
    ['code' => 'TT', 'name' => 'Trinidad und Tobago', 'region' => 'Karibik', 'languages' => 'Englisch', 'currency_code' => 'TTD'],
    ['code' => 'PR', 'name' => 'Puerto Rico', 'region' => 'Karibik', 'languages' => 'Spanisch, Englisch', 'currency_code' => 'USD'],
    ['code' => 'AW', 'name' => 'Aruba', 'region' => 'Karibik', 'languages' => 'Niederländisch, Papiamento', 'currency_code' => 'AWG'],
    ['code' => 'CW', 'name' => 'Curaçao', 'region' => 'Karibik', 'languages' => 'Niederländisch, Papiamento', 'currency_code' => 'ANG'],

    // SÜDAMERIKA (12 countries)
    ['code' => 'AR', 'name' => 'Argentinien', 'region' => 'Südamerika', 'languages' => 'Spanisch', 'currency_code' => 'ARS'],
    ['code' => 'BO', 'name' => 'Bolivien', 'region' => 'Südamerika', 'languages' => 'Spanisch, Quechua, Aymara', 'currency_code' => 'BOB'],
    ['code' => 'BR', 'name' => 'Brasilien', 'region' => 'Südamerika', 'languages' => 'Portugiesisch', 'currency_code' => 'BRL'],
    ['code' => 'CL', 'name' => 'Chile', 'region' => 'Südamerika', 'languages' => 'Spanisch', 'currency_code' => 'CLP'],
    ['code' => 'CO', 'name' => 'Kolumbien', 'region' => 'Südamerika', 'languages' => 'Spanisch', 'currency_code' => 'COP'],
    ['code' => 'EC', 'name' => 'Ecuador', 'region' => 'Südamerika', 'languages' => 'Spanisch', 'currency_code' => 'USD'],
    ['code' => 'GY', 'name' => 'Guyana', 'region' => 'Südamerika', 'languages' => 'Englisch', 'currency_code' => 'GYD'],
    ['code' => 'PY', 'name' => 'Paraguay', 'region' => 'Südamerika', 'languages' => 'Spanisch, Guaraní', 'currency_code' => 'PYG'],
    ['code' => 'PE', 'name' => 'Peru', 'region' => 'Südamerika', 'languages' => 'Spanisch', 'currency_code' => 'PEN'],
    ['code' => 'SR', 'name' => 'Suriname', 'region' => 'Südamerika', 'languages' => 'Niederländisch', 'currency_code' => 'SRD'],
    ['code' => 'UY', 'name' => 'Uruguay', 'region' => 'Südamerika', 'languages' => 'Spanisch', 'currency_code' => 'UYU'],
    ['code' => 'VE', 'name' => 'Venezuela', 'region' => 'Südamerika', 'languages' => 'Spanisch', 'currency_code' => 'VES'],

    // OZEANIEN (25 countries)
    ['code' => 'AU', 'name' => 'Australien', 'region' => 'Ozeanien', 'languages' => 'Englisch', 'currency_code' => 'AUD'],
    ['code' => 'NZ', 'name' => 'Neuseeland', 'region' => 'Ozeanien', 'languages' => 'Englisch, Maori', 'currency_code' => 'NZD'],
    ['code' => 'FJ', 'name' => 'Fidschi', 'region' => 'Ozeanien', 'languages' => 'Englisch, Fidschianisch, Hindi', 'currency_code' => 'FJD'],
    ['code' => 'PG', 'name' => 'Papua-Neuguinea', 'region' => 'Ozeanien', 'languages' => 'Englisch, Tok Pisin', 'currency_code' => 'PGK'],
    ['code' => 'SB', 'name' => 'Salomonen', 'region' => 'Ozeanien', 'languages' => 'Englisch', 'currency_code' => 'SBD'],
    ['code' => 'VU', 'name' => 'Vanuatu', 'region' => 'Ozeanien', 'languages' => 'Bislama, Englisch, Französisch', 'currency_code' => 'VUV'],
    ['code' => 'WS', 'name' => 'Samoa', 'region' => 'Ozeanien', 'languages' => 'Samoanisch, Englisch', 'currency_code' => 'WST'],
    ['code' => 'TO', 'name' => 'Tonga', 'region' => 'Ozeanien', 'languages' => 'Tongaisch, Englisch', 'currency_code' => 'TOP'],
    ['code' => 'KI', 'name' => 'Kiribati', 'region' => 'Ozeanien', 'languages' => 'Gilbertesisch, Englisch', 'currency_code' => 'AUD'],
    ['code' => 'FM', 'name' => 'Mikronesien', 'region' => 'Ozeanien', 'languages' => 'Englisch', 'currency_code' => 'USD'],
    ['code' => 'MH', 'name' => 'Marshallinseln', 'region' => 'Ozeanien', 'languages' => 'Marshallisch, Englisch', 'currency_code' => 'USD'],
    ['code' => 'PW', 'name' => 'Palau', 'region' => 'Ozeanien', 'languages' => 'Palauisch, Englisch', 'currency_code' => 'USD'],
    ['code' => 'NR', 'name' => 'Nauru', 'region' => 'Ozeanien', 'languages' => 'Nauruisch, Englisch', 'currency_code' => 'AUD'],
    ['code' => 'TV', 'name' => 'Tuvalu', 'region' => 'Ozeanien', 'languages' => 'Tuvaluisch, Englisch', 'currency_code' => 'AUD'],
    ['code' => 'NC', 'name' => 'Neukaledonien', 'region' => 'Ozeanien', 'languages' => 'Französisch', 'currency_code' => 'XPF'],
    ['code' => 'PF', 'name' => 'Französisch-Polynesien', 'region' => 'Ozeanien', 'languages' => 'Französisch', 'currency_code' => 'XPF'],
    ['code' => 'GU', 'name' => 'Guam', 'region' => 'Ozeanien', 'languages' => 'Englisch, Chamorro', 'currency_code' => 'USD'],
];

// Function to seed countries into database
function seedCountries(int $shopId): array
{
    global $allCountries;

    $inserted = 0;
    $updated = 0;

    foreach ($allCountries as $country) {
        // Check if exists
        $existing = Database::fetch(
            "SELECT id FROM countries WHERE shop_id = ? AND code = ?",
            [$shopId, $country['code']]
        );

        if ($existing) {
            // Update
            Database::query(
                "UPDATE countries SET name = ?, region = ?, languages = ?, currency_code = ? WHERE id = ?",
                [$country['name'], $country['region'], $country['languages'], $country['currency_code'], $existing['id']]
            );
            $updated++;
        } else {
            // Insert - default to inactive, non-default
            Database::query(
                "INSERT INTO countries (shop_id, code, name, region, languages, currency_code, is_active, is_default) VALUES (?, ?, ?, ?, ?, ?, 0, 0)",
                [$shopId, $country['code'], $country['name'], $country['region'], $country['languages'], $country['currency_code']]
            );
            $inserted++;
        }
    }

    return [
        'inserted' => $inserted,
        'updated' => $updated,
        'total' => count($allCountries)
    ];
}

// Export for use in other files
return $allCountries;
