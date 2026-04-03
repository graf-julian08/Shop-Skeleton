<?php
/**
 * Generate comprehensive locales.json from CLDR data
 * This creates all language-country combinations with real data
 */

// All Google Translate supported languages with their countries
$localeMatrix = [
    // English variants
    ['en_US', 'en', 'English', 'English', 'US', 'United States', 'USD', '$', 'm/d/Y', 'g:i A', 'America/New_York', false],
    ['en_GB', 'en', 'English', 'English', 'GB', 'United Kingdom', 'GBP', '£', 'd/m/Y', 'H:i', 'Europe/London', false],
    ['en_AU', 'en', 'English', 'English', 'AU', 'Australia', 'AUD', '$', 'd/m/Y', 'g:i A', 'Australia/Sydney', false],
    ['en_CA', 'en', 'English', 'English', 'CA', 'Canada', 'CAD', '$', 'Y-m-d', 'g:i A', 'America/Toronto', false],
    ['en_NZ', 'en', 'English', 'English', 'NZ', 'New Zealand', 'NZD', '$', 'd/m/Y', 'g:i A', 'Pacific/Auckland', false],
    ['en_IE', 'en', 'English', 'English', 'IE', 'Ireland', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Dublin', false],
    ['en_ZA', 'en', 'English', 'English', 'ZA', 'South Africa', 'ZAR', 'R', 'Y/m/d', 'H:i', 'Africa/Johannesburg', false],
    ['en_IN', 'en', 'English', 'English', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],
    ['en_SG', 'en', 'English', 'English', 'SG', 'Singapore', 'SGD', '$', 'd/m/Y', 'h:i A', 'Asia/Singapore', false],
    ['en_PH', 'en', 'English', 'English', 'PH', 'Philippines', 'PHP', '₱', 'm/d/Y', 'g:i A', 'Asia/Manila', false],
    ['en_HK', 'en', 'English', 'English', 'HK', 'Hong Kong', 'HKD', '$', 'd/m/Y', 'H:i', 'Asia/Hong_Kong', false],
    ['en_MY', 'en', 'English', 'English', 'MY', 'Malaysia', 'MYR', 'RM', 'd/m/Y', 'g:i A', 'Asia/Kuala_Lumpur', false],
    ['en_KE', 'en', 'English', 'English', 'KE', 'Kenya', 'KES', 'KSh', 'd/m/Y', 'H:i', 'Africa/Nairobi', false],
    ['en_NG', 'en', 'English', 'English', 'NG', 'Nigeria', 'NGN', '₦', 'd/m/Y', 'H:i', 'Africa/Lagos', false],
    ['en_GH', 'en', 'English', 'English', 'GH', 'Ghana', 'GHS', '₵', 'd/m/Y', 'H:i', 'Africa/Accra', false],
    ['en_PK', 'en', 'English', 'English', 'PK', 'Pakistan', 'PKR', '₨', 'd/m/Y', 'h:i A', 'Asia/Karachi', false],

    // German variants
    ['de_DE', 'de', 'German', 'Deutsch', 'DE', 'Germany', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Berlin', false],
    ['de_AT', 'de', 'German', 'Deutsch', 'AT', 'Austria', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Vienna', false],
    ['de_CH', 'de', 'German', 'Deutsch', 'CH', 'Switzerland', 'CHF', 'CHF', 'd.m.Y', 'H:i', 'Europe/Zurich', false],
    ['de_LI', 'de', 'German', 'Deutsch', 'LI', 'Liechtenstein', 'CHF', 'CHF', 'd.m.Y', 'H:i', 'Europe/Vaduz', false],
    ['de_LU', 'de', 'German', 'Deutsch', 'LU', 'Luxembourg', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Luxembourg', false],
    ['de_BE', 'de', 'German', 'Deutsch', 'BE', 'Belgium', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Brussels', false],

    // French variants
    ['fr_FR', 'fr', 'French', 'Français', 'FR', 'France', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Paris', false],
    ['fr_BE', 'fr', 'French', 'Français', 'BE', 'Belgium', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Brussels', false],
    ['fr_CH', 'fr', 'French', 'Français', 'CH', 'Switzerland', 'CHF', 'CHF', 'd.m.Y', 'H:i', 'Europe/Zurich', false],
    ['fr_CA', 'fr', 'French', 'Français', 'CA', 'Canada', 'CAD', '$', 'Y-m-d', 'H:i', 'America/Montreal', false],
    ['fr_LU', 'fr', 'French', 'Français', 'LU', 'Luxembourg', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Luxembourg', false],
    ['fr_MC', 'fr', 'French', 'Français', 'MC', 'Monaco', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Monaco', false],
    ['fr_SN', 'fr', 'French', 'Français', 'SN', 'Senegal', 'XOF', 'CFA', 'd/m/Y', 'H:i', 'Africa/Dakar', false],
    ['fr_CI', 'fr', 'French', 'Français', 'CI', 'Ivory Coast', 'XOF', 'CFA', 'd/m/Y', 'H:i', 'Africa/Abidjan', false],
    ['fr_ML', 'fr', 'French', 'Français', 'ML', 'Mali', 'XOF', 'CFA', 'd/m/Y', 'H:i', 'Africa/Bamako', false],
    ['fr_CM', 'fr', 'French', 'Français', 'CM', 'Cameroon', 'XAF', 'FCFA', 'd/m/Y', 'H:i', 'Africa/Douala', false],
    ['fr_MG', 'fr', 'French', 'Français', 'MG', 'Madagascar', 'MGA', 'Ar', 'd/m/Y', 'H:i', 'Indian/Antananarivo', false],
    ['fr_HT', 'fr', 'French', 'Français', 'HT', 'Haiti', 'HTG', 'G', 'd/m/Y', 'H:i', 'America/Port-au-Prince', false],

    // Spanish variants
    ['es_ES', 'es', 'Spanish', 'Español', 'ES', 'Spain', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Madrid', false],
    ['es_MX', 'es', 'Spanish', 'Español', 'MX', 'Mexico', 'MXN', '$', 'd/m/Y', 'H:i', 'America/Mexico_City', false],
    ['es_AR', 'es', 'Spanish', 'Español', 'AR', 'Argentina', 'ARS', '$', 'd/m/Y', 'H:i', 'America/Buenos_Aires', false],
    ['es_CO', 'es', 'Spanish', 'Español', 'CO', 'Colombia', 'COP', '$', 'd/m/Y', 'H:i', 'America/Bogota', false],
    ['es_PE', 'es', 'Spanish', 'Español', 'PE', 'Peru', 'PEN', 'S/', 'd/m/Y', 'H:i', 'America/Lima', false],
    ['es_VE', 'es', 'Spanish', 'Español', 'VE', 'Venezuela', 'VES', 'Bs', 'd/m/Y', 'H:i', 'America/Caracas', false],
    ['es_CL', 'es', 'Spanish', 'Español', 'CL', 'Chile', 'CLP', '$', 'd-m-Y', 'H:i', 'America/Santiago', false],
    ['es_EC', 'es', 'Spanish', 'Español', 'EC', 'Ecuador', 'USD', '$', 'd/m/Y', 'H:i', 'America/Guayaquil', false],
    ['es_GT', 'es', 'Spanish', 'Español', 'GT', 'Guatemala', 'GTQ', 'Q', 'd/m/Y', 'H:i', 'America/Guatemala', false],
    ['es_CU', 'es', 'Spanish', 'Español', 'CU', 'Cuba', 'CUP', '$', 'd/m/Y', 'H:i', 'America/Havana', false],
    ['es_BO', 'es', 'Spanish', 'Español', 'BO', 'Bolivia', 'BOB', 'Bs', 'd/m/Y', 'H:i', 'America/La_Paz', false],
    ['es_DO', 'es', 'Spanish', 'Español', 'DO', 'Dominican Republic', 'DOP', '$', 'd/m/Y', 'H:i', 'America/Santo_Domingo', false],
    ['es_HN', 'es', 'Spanish', 'Español', 'HN', 'Honduras', 'HNL', 'L', 'd/m/Y', 'H:i', 'America/Tegucigalpa', false],
    ['es_PY', 'es', 'Spanish', 'Español', 'PY', 'Paraguay', 'PYG', '₲', 'd/m/Y', 'H:i', 'America/Asuncion', false],
    ['es_SV', 'es', 'Spanish', 'Español', 'SV', 'El Salvador', 'USD', '$', 'd/m/Y', 'H:i', 'America/El_Salvador', false],
    ['es_NI', 'es', 'Spanish', 'Español', 'NI', 'Nicaragua', 'NIO', 'C$', 'd/m/Y', 'H:i', 'America/Managua', false],
    ['es_CR', 'es', 'Spanish', 'Español', 'CR', 'Costa Rica', 'CRC', '₡', 'd/m/Y', 'H:i', 'America/Costa_Rica', false],
    ['es_PA', 'es', 'Spanish', 'Español', 'PA', 'Panama', 'PAB', 'B/', 'd/m/Y', 'H:i', 'America/Panama', false],
    ['es_UY', 'es', 'Spanish', 'Español', 'UY', 'Uruguay', 'UYU', '$', 'd/m/Y', 'H:i', 'America/Montevideo', false],
    ['es_PR', 'es', 'Spanish', 'Español', 'PR', 'Puerto Rico', 'USD', '$', 'm/d/Y', 'g:i A', 'America/Puerto_Rico', false],
    ['es_US', 'es', 'Spanish', 'Español', 'US', 'United States', 'USD', '$', 'm/d/Y', 'g:i A', 'America/New_York', false],

    // Portuguese variants
    ['pt_BR', 'pt', 'Portuguese', 'Português', 'BR', 'Brazil', 'BRL', 'R$', 'd/m/Y', 'H:i', 'America/Sao_Paulo', false],
    ['pt_PT', 'pt', 'Portuguese', 'Português', 'PT', 'Portugal', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Lisbon', false],
    ['pt_AO', 'pt', 'Portuguese', 'Português', 'AO', 'Angola', 'AOA', 'Kz', 'd/m/Y', 'H:i', 'Africa/Luanda', false],
    ['pt_MZ', 'pt', 'Portuguese', 'Português', 'MZ', 'Mozambique', 'MZN', 'MT', 'd/m/Y', 'H:i', 'Africa/Maputo', false],
    ['pt_CV', 'pt', 'Portuguese', 'Português', 'CV', 'Cape Verde', 'CVE', '$', 'd/m/Y', 'H:i', 'Atlantic/Cape_Verde', false],
    ['pt_GW', 'pt', 'Portuguese', 'Português', 'GW', 'Guinea-Bissau', 'XOF', 'CFA', 'd/m/Y', 'H:i', 'Africa/Bissau', false],

    // Italian variants
    ['it_IT', 'it', 'Italian', 'Italiano', 'IT', 'Italy', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Rome', false],
    ['it_CH', 'it', 'Italian', 'Italiano', 'CH', 'Switzerland', 'CHF', 'CHF', 'd.m.Y', 'H:i', 'Europe/Zurich', false],
    ['it_SM', 'it', 'Italian', 'Italiano', 'SM', 'San Marino', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/San_Marino', false],
    ['it_VA', 'it', 'Italian', 'Italiano', 'VA', 'Vatican City', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Vatican', false],

    // Dutch variants
    ['nl_NL', 'nl', 'Dutch', 'Nederlands', 'NL', 'Netherlands', 'EUR', '€', 'd-m-Y', 'H:i', 'Europe/Amsterdam', false],
    ['nl_BE', 'nl', 'Dutch', 'Nederlands', 'BE', 'Belgium', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Brussels', false],
    ['nl_SR', 'nl', 'Dutch', 'Nederlands', 'SR', 'Suriname', 'SRD', '$', 'd-m-Y', 'H:i', 'America/Paramaribo', false],
    ['nl_AW', 'nl', 'Dutch', 'Nederlands', 'AW', 'Aruba', 'AWG', 'ƒ', 'd-m-Y', 'H:i', 'America/Aruba', false],
    ['nl_CW', 'nl', 'Dutch', 'Nederlands', 'CW', 'Curaçao', 'ANG', 'ƒ', 'd-m-Y', 'H:i', 'America/Curacao', false],

    // Russian variants
    ['ru_RU', 'ru', 'Russian', 'Русский', 'RU', 'Russia', 'RUB', '₽', 'd.m.Y', 'H:i', 'Europe/Moscow', false],
    ['ru_BY', 'ru', 'Russian', 'Русский', 'BY', 'Belarus', 'BYN', 'Br', 'd.m.Y', 'H:i', 'Europe/Minsk', false],
    ['ru_KZ', 'ru', 'Russian', 'Русский', 'KZ', 'Kazakhstan', 'KZT', '₸', 'd.m.Y', 'H:i', 'Asia/Almaty', false],
    ['ru_KG', 'ru', 'Russian', 'Русский', 'KG', 'Kyrgyzstan', 'KGS', 'с', 'd.m.Y', 'H:i', 'Asia/Bishkek', false],
    ['ru_UA', 'ru', 'Russian', 'Русский', 'UA', 'Ukraine', 'UAH', '₴', 'd.m.Y', 'H:i', 'Europe/Kiev', false],
    ['ru_MD', 'ru', 'Russian', 'Русский', 'MD', 'Moldova', 'MDL', 'L', 'd.m.Y', 'H:i', 'Europe/Chisinau', false],

    // Chinese variants
    ['zh_CN', 'zh', 'Chinese', '简体中文', 'CN', 'China', 'CNY', '¥', 'Y年m月d日', 'H:i', 'Asia/Shanghai', false],
    ['zh_TW', 'zh', 'Chinese', '繁體中文', 'TW', 'Taiwan', 'TWD', 'NT$', 'Y年m月d日', 'H:i', 'Asia/Taipei', false],
    ['zh_HK', 'zh', 'Chinese', '繁體中文', 'HK', 'Hong Kong', 'HKD', '$', 'd/m/Y', 'H:i', 'Asia/Hong_Kong', false],
    ['zh_SG', 'zh', 'Chinese', '简体中文', 'SG', 'Singapore', 'SGD', '$', 'd/m/Y', 'H:i', 'Asia/Singapore', false],
    ['zh_MO', 'zh', 'Chinese', '繁體中文', 'MO', 'Macau', 'MOP', 'MOP$', 'd/m/Y', 'H:i', 'Asia/Macau', false],

    // Japanese
    ['ja_JP', 'ja', 'Japanese', '日本語', 'JP', 'Japan', 'JPY', '¥', 'Y/m/d', 'H:i', 'Asia/Tokyo', false],

    // Korean
    ['ko_KR', 'ko', 'Korean', '한국어', 'KR', 'South Korea', 'KRW', '₩', 'Y. m. d.', 'H:i', 'Asia/Seoul', false],
    ['ko_KP', 'ko', 'Korean', '한국어', 'KP', 'North Korea', 'KPW', '₩', 'Y. m. d.', 'H:i', 'Asia/Pyongyang', false],

    // Arabic variants
    ['ar_SA', 'ar', 'Arabic', 'العربية', 'SA', 'Saudi Arabia', 'SAR', '﷼', 'd/m/Y', 'H:i', 'Asia/Riyadh', true],
    ['ar_AE', 'ar', 'Arabic', 'العربية', 'AE', 'United Arab Emirates', 'AED', 'د.إ', 'd/m/Y', 'H:i', 'Asia/Dubai', true],
    ['ar_EG', 'ar', 'Arabic', 'العربية', 'EG', 'Egypt', 'EGP', 'E£', 'd/m/Y', 'H:i', 'Africa/Cairo', true],
    ['ar_MA', 'ar', 'Arabic', 'العربية', 'MA', 'Morocco', 'MAD', 'د.م.', 'd/m/Y', 'H:i', 'Africa/Casablanca', true],
    ['ar_DZ', 'ar', 'Arabic', 'العربية', 'DZ', 'Algeria', 'DZD', 'د.ج', 'd/m/Y', 'H:i', 'Africa/Algiers', true],
    ['ar_TN', 'ar', 'Arabic', 'العربية', 'TN', 'Tunisia', 'TND', 'د.ت', 'd/m/Y', 'H:i', 'Africa/Tunis', true],
    ['ar_LY', 'ar', 'Arabic', 'العربية', 'LY', 'Libya', 'LYD', 'ل.د', 'd/m/Y', 'H:i', 'Africa/Tripoli', true],
    ['ar_JO', 'ar', 'Arabic', 'العربية', 'JO', 'Jordan', 'JOD', 'د.ا', 'd/m/Y', 'H:i', 'Asia/Amman', true],
    ['ar_LB', 'ar', 'Arabic', 'العربية', 'LB', 'Lebanon', 'LBP', 'ل.ل', 'd/m/Y', 'H:i', 'Asia/Beirut', true],
    ['ar_SY', 'ar', 'Arabic', 'العربية', 'SY', 'Syria', 'SYP', '£', 'd/m/Y', 'H:i', 'Asia/Damascus', true],
    ['ar_IQ', 'ar', 'Arabic', 'العربية', 'IQ', 'Iraq', 'IQD', 'ع.د', 'd/m/Y', 'H:i', 'Asia/Baghdad', true],
    ['ar_KW', 'ar', 'Arabic', 'العربية', 'KW', 'Kuwait', 'KWD', 'د.ك', 'd/m/Y', 'H:i', 'Asia/Kuwait', true],
    ['ar_BH', 'ar', 'Arabic', 'العربية', 'BH', 'Bahrain', 'BHD', 'د.ب', 'd/m/Y', 'H:i', 'Asia/Bahrain', true],
    ['ar_QA', 'ar', 'Arabic', 'العربية', 'QA', 'Qatar', 'QAR', 'ر.ق', 'd/m/Y', 'H:i', 'Asia/Qatar', true],
    ['ar_OM', 'ar', 'Arabic', 'العربية', 'OM', 'Oman', 'OMR', 'ر.ع.', 'd/m/Y', 'H:i', 'Asia/Muscat', true],
    ['ar_YE', 'ar', 'Arabic', 'العربية', 'YE', 'Yemen', 'YER', '﷼', 'd/m/Y', 'H:i', 'Asia/Aden', true],
    ['ar_SD', 'ar', 'Arabic', 'العربية', 'SD', 'Sudan', 'SDG', 'ج.س', 'd/m/Y', 'H:i', 'Africa/Khartoum', true],

    // Hindi
    ['hi_IN', 'hi', 'Hindi', 'हिन्दी', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],

    // Bengali
    ['bn_BD', 'bn', 'Bengali', 'বাংলা', 'BD', 'Bangladesh', 'BDT', '৳', 'd/m/Y', 'h:i A', 'Asia/Dhaka', false],
    ['bn_IN', 'bn', 'Bengali', 'বাংলা', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],

    // Turkish
    ['tr_TR', 'tr', 'Turkish', 'Türkçe', 'TR', 'Turkey', 'TRY', '₺', 'd.m.Y', 'H:i', 'Europe/Istanbul', false],
    ['tr_CY', 'tr', 'Turkish', 'Türkçe', 'CY', 'Cyprus', 'EUR', '€', 'd.m.Y', 'H:i', 'Asia/Nicosia', false],

    // Vietnamese
    ['vi_VN', 'vi', 'Vietnamese', 'Tiếng Việt', 'VN', 'Vietnam', 'VND', '₫', 'd/m/Y', 'H:i', 'Asia/Ho_Chi_Minh', false],

    // Thai
    ['th_TH', 'th', 'Thai', 'ไทย', 'TH', 'Thailand', 'THB', '฿', 'd/m/Y', 'H:i', 'Asia/Bangkok', false],

    // Indonesian
    ['id_ID', 'id', 'Indonesian', 'Bahasa Indonesia', 'ID', 'Indonesia', 'IDR', 'Rp', 'd/m/Y', 'H:i', 'Asia/Jakarta', false],

    // Malay variants
    ['ms_MY', 'ms', 'Malay', 'Bahasa Melayu', 'MY', 'Malaysia', 'MYR', 'RM', 'd/m/Y', 'g:i A', 'Asia/Kuala_Lumpur', false],
    ['ms_SG', 'ms', 'Malay', 'Bahasa Melayu', 'SG', 'Singapore', 'SGD', '$', 'd/m/Y', 'h:i A', 'Asia/Singapore', false],
    ['ms_BN', 'ms', 'Malay', 'Bahasa Melayu', 'BN', 'Brunei', 'BND', '$', 'd/m/Y', 'H:i', 'Asia/Brunei', false],

    // Filipino
    ['fil_PH', 'fil', 'Filipino', 'Filipino', 'PH', 'Philippines', 'PHP', '₱', 'm/d/Y', 'g:i A', 'Asia/Manila', false],

    // Polish
    ['pl_PL', 'pl', 'Polish', 'Polski', 'PL', 'Poland', 'PLN', 'zł', 'd.m.Y', 'H:i', 'Europe/Warsaw', false],

    // Ukrainian
    ['uk_UA', 'uk', 'Ukrainian', 'Українська', 'UA', 'Ukraine', 'UAH', '₴', 'd.m.Y', 'H:i', 'Europe/Kiev', false],

    // Romanian
    ['ro_RO', 'ro', 'Romanian', 'Română', 'RO', 'Romania', 'RON', 'lei', 'd.m.Y', 'H:i', 'Europe/Bucharest', false],
    ['ro_MD', 'ro', 'Romanian', 'Română', 'MD', 'Moldova', 'MDL', 'L', 'd.m.Y', 'H:i', 'Europe/Chisinau', false],

    // Greek
    ['el_GR', 'el', 'Greek', 'Ελληνικά', 'GR', 'Greece', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Athens', false],
    ['el_CY', 'el', 'Greek', 'Ελληνικά', 'CY', 'Cyprus', 'EUR', '€', 'd/m/Y', 'H:i', 'Asia/Nicosia', false],

    // Czech
    ['cs_CZ', 'cs', 'Czech', 'Čeština', 'CZ', 'Czech Republic', 'CZK', 'Kč', 'd.m.Y', 'H:i', 'Europe/Prague', false],

    // Hungarian
    ['hu_HU', 'hu', 'Hungarian', 'Magyar', 'HU', 'Hungary', 'HUF', 'Ft', 'Y.m.d.', 'H:i', 'Europe/Budapest', false],

    // Swedish
    ['sv_SE', 'sv', 'Swedish', 'Svenska', 'SE', 'Sweden', 'SEK', 'kr', 'Y-m-d', 'H:i', 'Europe/Stockholm', false],
    ['sv_FI', 'sv', 'Swedish', 'Svenska', 'FI', 'Finland', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Helsinki', false],

    // Norwegian
    ['no_NO', 'no', 'Norwegian', 'Norsk', 'NO', 'Norway', 'NOK', 'kr', 'd.m.Y', 'H:i', 'Europe/Oslo', false],
    ['nb_NO', 'nb', 'Norwegian Bokmål', 'Norsk bokmål', 'NO', 'Norway', 'NOK', 'kr', 'd.m.Y', 'H:i', 'Europe/Oslo', false],

    // Danish
    ['da_DK', 'da', 'Danish', 'Dansk', 'DK', 'Denmark', 'DKK', 'kr', 'd.m.Y', 'H:i', 'Europe/Copenhagen', false],

    // Finnish
    ['fi_FI', 'fi', 'Finnish', 'Suomi', 'FI', 'Finland', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Helsinki', false],

    // Hebrew
    ['he_IL', 'he', 'Hebrew', 'עברית', 'IL', 'Israel', 'ILS', '₪', 'd/m/Y', 'H:i', 'Asia/Jerusalem', true],

    // Persian
    ['fa_IR', 'fa', 'Persian', 'فارسی', 'IR', 'Iran', 'IRR', '﷼', 'Y/m/d', 'H:i', 'Asia/Tehran', true],
    ['fa_AF', 'fa', 'Persian', 'فارسی', 'AF', 'Afghanistan', 'AFN', '؋', 'Y/m/d', 'H:i', 'Asia/Kabul', true],

    // Urdu
    ['ur_PK', 'ur', 'Urdu', 'اردو', 'PK', 'Pakistan', 'PKR', '₨', 'd/m/Y', 'h:i A', 'Asia/Karachi', true],
    ['ur_IN', 'ur', 'Urdu', 'اردو', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', true],

    // Tamil
    ['ta_IN', 'ta', 'Tamil', 'தமிழ்', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],
    ['ta_SG', 'ta', 'Tamil', 'தமிழ்', 'SG', 'Singapore', 'SGD', '$', 'd/m/Y', 'h:i A', 'Asia/Singapore', false],
    ['ta_LK', 'ta', 'Tamil', 'தமிழ்', 'LK', 'Sri Lanka', 'LKR', 'Rs', 'd/m/Y', 'H:i', 'Asia/Colombo', false],
    ['ta_MY', 'ta', 'Tamil', 'தமிழ்', 'MY', 'Malaysia', 'MYR', 'RM', 'd/m/Y', 'g:i A', 'Asia/Kuala_Lumpur', false],

    // Telugu
    ['te_IN', 'te', 'Telugu', 'తెలుగు', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],

    // Marathi
    ['mr_IN', 'mr', 'Marathi', 'मराठी', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],

    // Gujarati
    ['gu_IN', 'gu', 'Gujarati', 'ગુજરાતી', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],

    // Kannada
    ['kn_IN', 'kn', 'Kannada', 'ಕನ್ನಡ', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],

    // Malayalam
    ['ml_IN', 'ml', 'Malayalam', 'മലയാളം', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],

    // Punjabi
    ['pa_IN', 'pa', 'Punjabi', 'ਪੰਜਾਬੀ', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],
    ['pa_PK', 'pa', 'Punjabi', 'پنجابی', 'PK', 'Pakistan', 'PKR', '₨', 'd/m/Y', 'h:i A', 'Asia/Karachi', true],

    // Odia
    ['or_IN', 'or', 'Odia', 'ଓଡ଼ିଆ', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],

    // Sinhala
    ['si_LK', 'si', 'Sinhala', 'සිංහල', 'LK', 'Sri Lanka', 'LKR', 'Rs', 'd/m/Y', 'H:i', 'Asia/Colombo', false],

    // Nepali
    ['ne_NP', 'ne', 'Nepali', 'नेपाली', 'NP', 'Nepal', 'NPR', '₨', 'Y/m/d', 'H:i', 'Asia/Kathmandu', false],
    ['ne_IN', 'ne', 'Nepali', 'नेपाली', 'IN', 'India', 'INR', '₹', 'd/m/Y', 'h:i A', 'Asia/Kolkata', false],

    // Swahili
    ['sw_KE', 'sw', 'Swahili', 'Kiswahili', 'KE', 'Kenya', 'KES', 'KSh', 'd/m/Y', 'H:i', 'Africa/Nairobi', false],
    ['sw_TZ', 'sw', 'Swahili', 'Kiswahili', 'TZ', 'Tanzania', 'TZS', 'TSh', 'd/m/Y', 'H:i', 'Africa/Dar_es_Salaam', false],
    ['sw_UG', 'sw', 'Swahili', 'Kiswahili', 'UG', 'Uganda', 'UGX', 'USh', 'd/m/Y', 'H:i', 'Africa/Kampala', false],

    // Afrikaans
    ['af_ZA', 'af', 'Afrikaans', 'Afrikaans', 'ZA', 'South Africa', 'ZAR', 'R', 'Y/m/d', 'H:i', 'Africa/Johannesburg', false],

    // Zulu
    ['zu_ZA', 'zu', 'Zulu', 'isiZulu', 'ZA', 'South Africa', 'ZAR', 'R', 'Y/m/d', 'H:i', 'Africa/Johannesburg', false],

    // Xhosa
    ['xh_ZA', 'xh', 'Xhosa', 'isiXhosa', 'ZA', 'South Africa', 'ZAR', 'R', 'Y/m/d', 'H:i', 'Africa/Johannesburg', false],

    // Amharic
    ['am_ET', 'am', 'Amharic', 'አማርኛ', 'ET', 'Ethiopia', 'ETB', 'Br', 'd/m/Y', 'H:i', 'Africa/Addis_Ababa', false],

    // Hausa
    ['ha_NG', 'ha', 'Hausa', 'Hausa', 'NG', 'Nigeria', 'NGN', '₦', 'd/m/Y', 'H:i', 'Africa/Lagos', false],
    ['ha_GH', 'ha', 'Hausa', 'Hausa', 'GH', 'Ghana', 'GHS', '₵', 'd/m/Y', 'H:i', 'Africa/Accra', false],

    // Yoruba
    ['yo_NG', 'yo', 'Yoruba', 'Yorùbá', 'NG', 'Nigeria', 'NGN', '₦', 'd/m/Y', 'H:i', 'Africa/Lagos', false],

    // Igbo
    ['ig_NG', 'ig', 'Igbo', 'Igbo', 'NG', 'Nigeria', 'NGN', '₦', 'd/m/Y', 'H:i', 'Africa/Lagos', false],

    // Somali
    ['so_SO', 'so', 'Somali', 'Soomaali', 'SO', 'Somalia', 'SOS', 'Sh', 'd/m/Y', 'H:i', 'Africa/Mogadishu', false],
    ['so_ET', 'so', 'Somali', 'Soomaali', 'ET', 'Ethiopia', 'ETB', 'Br', 'd/m/Y', 'H:i', 'Africa/Addis_Ababa', false],
    ['so_KE', 'so', 'Somali', 'Soomaali', 'KE', 'Kenya', 'KES', 'KSh', 'd/m/Y', 'H:i', 'Africa/Nairobi', false],

    // Catalan
    ['ca_ES', 'ca', 'Catalan', 'Català', 'ES', 'Spain', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Madrid', false],
    ['ca_AD', 'ca', 'Catalan', 'Català', 'AD', 'Andorra', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Andorra', false],

    // Basque
    ['eu_ES', 'eu', 'Basque', 'Euskara', 'ES', 'Spain', 'EUR', '€', 'Y/m/d', 'H:i', 'Europe/Madrid', false],

    // Galician
    ['gl_ES', 'gl', 'Galician', 'Galego', 'ES', 'Spain', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Madrid', false],

    // Welsh
    ['cy_GB', 'cy', 'Welsh', 'Cymraeg', 'GB', 'United Kingdom', 'GBP', '£', 'd/m/Y', 'H:i', 'Europe/London', false],

    // Irish
    ['ga_IE', 'ga', 'Irish', 'Gaeilge', 'IE', 'Ireland', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Dublin', false],

    // Scottish Gaelic
    ['gd_GB', 'gd', 'Scottish Gaelic', 'Gàidhlig', 'GB', 'United Kingdom', 'GBP', '£', 'd/m/Y', 'H:i', 'Europe/London', false],

    // Icelandic
    ['is_IS', 'is', 'Icelandic', 'Íslenska', 'IS', 'Iceland', 'ISK', 'kr', 'd.m.Y', 'H:i', 'Atlantic/Reykjavik', false],

    // Latvian
    ['lv_LV', 'lv', 'Latvian', 'Latviešu', 'LV', 'Latvia', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Riga', false],

    // Lithuanian
    ['lt_LT', 'lt', 'Lithuanian', 'Lietuvių', 'LT', 'Lithuania', 'EUR', '€', 'Y-m-d', 'H:i', 'Europe/Vilnius', false],

    // Estonian
    ['et_EE', 'et', 'Estonian', 'Eesti', 'EE', 'Estonia', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Tallinn', false],

    // Slovak
    ['sk_SK', 'sk', 'Slovak', 'Slovenčina', 'SK', 'Slovakia', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Bratislava', false],

    // Slovenian
    ['sl_SI', 'sl', 'Slovenian', 'Slovenščina', 'SI', 'Slovenia', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Ljubljana', false],

    // Croatian
    ['hr_HR', 'hr', 'Croatian', 'Hrvatski', 'HR', 'Croatia', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Zagreb', false],
    ['hr_BA', 'hr', 'Croatian', 'Hrvatski', 'BA', 'Bosnia and Herzegovina', 'BAM', 'KM', 'd.m.Y', 'H:i', 'Europe/Sarajevo', false],

    // Serbian
    ['sr_RS', 'sr', 'Serbian', 'Српски', 'RS', 'Serbia', 'RSD', 'дин.', 'd.m.Y.', 'H:i', 'Europe/Belgrade', false],
    ['sr_BA', 'sr', 'Serbian', 'Српски', 'BA', 'Bosnia and Herzegovina', 'BAM', 'KM', 'd.m.Y', 'H:i', 'Europe/Sarajevo', false],
    ['sr_ME', 'sr', 'Serbian', 'Српски', 'ME', 'Montenegro', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Podgorica', false],

    // Bosnian
    ['bs_BA', 'bs', 'Bosnian', 'Bosanski', 'BA', 'Bosnia and Herzegovina', 'BAM', 'KM', 'd.m.Y', 'H:i', 'Europe/Sarajevo', false],

    // Macedonian
    ['mk_MK', 'mk', 'Macedonian', 'Македонски', 'MK', 'North Macedonia', 'MKD', 'ден', 'd.m.Y', 'H:i', 'Europe/Skopje', false],

    // Bulgarian
    ['bg_BG', 'bg', 'Bulgarian', 'Български', 'BG', 'Bulgaria', 'BGN', 'лв', 'd.m.Y', 'H:i', 'Europe/Sofia', false],

    // Albanian
    ['sq_AL', 'sq', 'Albanian', 'Shqip', 'AL', 'Albania', 'ALL', 'L', 'd.m.Y', 'H:i', 'Europe/Tirane', false],
    ['sq_XK', 'sq', 'Albanian', 'Shqip', 'XK', 'Kosovo', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Belgrade', false],
    ['sq_MK', 'sq', 'Albanian', 'Shqip', 'MK', 'North Macedonia', 'MKD', 'ден', 'd.m.Y', 'H:i', 'Europe/Skopje', false],

    // Georgian
    ['ka_GE', 'ka', 'Georgian', 'ქართული', 'GE', 'Georgia', 'GEL', '₾', 'd.m.Y', 'H:i', 'Asia/Tbilisi', false],

    // Armenian
    ['hy_AM', 'hy', 'Armenian', 'Հայերdelays', 'AM', 'Armenia', 'AMD', '֏', 'd.m.Y', 'H:i', 'Asia/Yerevan', false],

    // Azerbaijani
    ['az_AZ', 'az', 'Azerbaijani', 'Azərbaycan', 'AZ', 'Azerbaijan', 'AZN', '₼', 'd.m.Y', 'H:i', 'Asia/Baku', false],

    // Kazakh
    ['kk_KZ', 'kk', 'Kazakh', 'Қазақша', 'KZ', 'Kazakhstan', 'KZT', '₸', 'd.m.Y', 'H:i', 'Asia/Almaty', false],

    // Uzbek
    ['uz_UZ', 'uz', 'Uzbek', 'Oʻzbek', 'UZ', 'Uzbekistan', 'UZS', 'soʻm', 'd.m.Y', 'H:i', 'Asia/Tashkent', false],

    // Kyrgyz
    ['ky_KG', 'ky', 'Kyrgyz', 'Кыргызча', 'KG', 'Kyrgyzstan', 'KGS', 'с', 'd.m.Y', 'H:i', 'Asia/Bishkek', false],

    // Tajik
    ['tg_TJ', 'tg', 'Tajik', 'Тоҷикӣ', 'TJ', 'Tajikistan', 'TJS', 'с.', 'd.m.Y', 'H:i', 'Asia/Dushanbe', false],

    // Turkmen
    ['tk_TM', 'tk', 'Turkmen', 'Türkmen', 'TM', 'Turkmenistan', 'TMT', 'T', 'd.m.Y', 'H:i', 'Asia/Ashgabat', false],

    // Mongolian
    ['mn_MN', 'mn', 'Mongolian', 'Монгол', 'MN', 'Mongolia', 'MNT', '₮', 'Y.m.d', 'H:i', 'Asia/Ulaanbaatar', false],

    // Burmese
    ['my_MM', 'my', 'Burmese', 'မြန်မာ', 'MM', 'Myanmar', 'MMK', 'K', 'd/m/Y', 'H:i', 'Asia/Yangon', false],

    // Khmer
    ['km_KH', 'km', 'Khmer', 'ខ្មែរ', 'KH', 'Cambodia', 'KHR', '៛', 'd/m/Y', 'H:i', 'Asia/Phnom_Penh', false],

    // Lao
    ['lo_LA', 'lo', 'Lao', 'ລາວ', 'LA', 'Laos', 'LAK', '₭', 'd/m/Y', 'H:i', 'Asia/Vientiane', false],

    // Luxembourgish
    ['lb_LU', 'lb', 'Luxembourgish', 'Lëtzebuergesch', 'LU', 'Luxembourg', 'EUR', '€', 'd.m.Y', 'H:i', 'Europe/Luxembourg', false],

    // Maltese
    ['mt_MT', 'mt', 'Maltese', 'Malti', 'MT', 'Malta', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Malta', false],

    // Frisian
    ['fy_NL', 'fy', 'Frisian', 'Frysk', 'NL', 'Netherlands', 'EUR', '€', 'd-m-Y', 'H:i', 'Europe/Amsterdam', false],

    // Corsican
    ['co_FR', 'co', 'Corsican', 'Corsu', 'FR', 'France', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Paris', false],

    // Maori
    ['mi_NZ', 'mi', 'Maori', 'Māori', 'NZ', 'New Zealand', 'NZD', '$', 'd/m/Y', 'g:i A', 'Pacific/Auckland', false],

    // Hawaiian
    ['haw_US', 'haw', 'Hawaiian', 'ʻŌlelo Hawaiʻi', 'US', 'United States', 'USD', '$', 'm/d/Y', 'g:i A', 'Pacific/Honolulu', false],

    // Samoan
    ['sm_WS', 'sm', 'Samoan', 'Gagana Samoa', 'WS', 'Samoa', 'WST', 'T', 'd/m/Y', 'H:i', 'Pacific/Apia', false],

    // Esperanto (global)
    ['eo_001', 'eo', 'Esperanto', 'Esperanto', '001', 'World', 'EUR', '€', 'Y-m-d', 'H:i', 'UTC', false],

    // Latin (global)
    ['la_VA', 'la', 'Latin', 'Latina', 'VA', 'Vatican City', 'EUR', '€', 'd/m/Y', 'H:i', 'Europe/Vatican', false],
];

// Build the JSON structure
$locales = [];
foreach ($localeMatrix as $loc) {
    $locales[] = [
        'code' => $loc[0],
        'language_code' => $loc[1],
        'language_name' => $loc[2],
        'language_native' => $loc[3],
        'country_code' => $loc[4],
        'country_name' => $loc[5],
        'currency_code' => $loc[6],
        'currency_symbol' => $loc[7],
        'date_format' => $loc[8],
        'time_format' => $loc[9],
        'timezone' => $loc[10],
        'rtl' => $loc[11]
    ];
}

// Google Translate supported language codes (for validation)
$googleTranslateLanguages = [
    'af',
    'sq',
    'am',
    'ar',
    'hy',
    'az',
    'eu',
    'be',
    'bn',
    'bs',
    'bg',
    'ca',
    'ceb',
    'zh',
    'co',
    'hr',
    'cs',
    'da',
    'nl',
    'en',
    'eo',
    'et',
    'fi',
    'fr',
    'fy',
    'gl',
    'ka',
    'de',
    'el',
    'gu',
    'ht',
    'ha',
    'haw',
    'he',
    'hi',
    'hmn',
    'hu',
    'is',
    'ig',
    'id',
    'ga',
    'it',
    'ja',
    'jv',
    'kn',
    'kk',
    'km',
    'rw',
    'ko',
    'ku',
    'ky',
    'lo',
    'la',
    'lv',
    'lt',
    'lb',
    'mk',
    'mg',
    'ms',
    'ml',
    'mt',
    'mi',
    'mr',
    'mn',
    'my',
    'ne',
    'no',
    'nb',
    'ny',
    'or',
    'ps',
    'fa',
    'pl',
    'pt',
    'pa',
    'ro',
    'ru',
    'sm',
    'gd',
    'sr',
    'st',
    'sn',
    'sd',
    'si',
    'sk',
    'sl',
    'so',
    'es',
    'su',
    'sw',
    'sv',
    'tl',
    'fil',
    'tg',
    'ta',
    'tt',
    'te',
    'th',
    'tr',
    'tk',
    'uk',
    'ur',
    'ug',
    'uz',
    'vi',
    'cy',
    'xh',
    'yi',
    'yo',
    'zu'
];

$output = [
    'google_translate_supported' => $googleTranslateLanguages,
    'locales' => $locales,
    'total_locales' => count($locales),
    'generated_at' => date('Y-m-d H:i:s')
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
