<?php
// Enhanced Language System - Complete translations for all three languages

// Check session first, then cookie, then default to English
if (!isset($_SESSION['language'])) {
    if (isset($_COOKIE['preferred_language']) && in_array($_COOKIE['preferred_language'], ['en', 'fr', 'rw'])) {
        $_SESSION['language'] = $_COOKIE['preferred_language'];
    } else {
        $_SESSION['language'] = 'en'; // Default to English
    }
}

$lang = $_SESSION['language'];

// Complete translation arrays for all interface elements
$translations = array(
    'en' => array(
        'site_title' => 'International Commerce System',
        'logo_text' => 'International Commerce',
        'welcome_message' => 'Welcome to Our International Commerce Platform',
        'hero_description' => 'Connect buyers and sellers across languages with our innovative local language support system.',
        'featured_products' => 'Featured Products',
        'search_placeholder' => 'Search products...',
        'contact_us' => 'Contact Us',
        'home' => 'Home',
        'register' => 'Register',
        'login' => 'Login',
        'current_language' => 'Current Language',
        'choose_language' => 'Choose Language',
        'translator' => 'Translator',
        'page_title' => 'Language Translator',
        'translator_title' => 'Multi-Language Translator',
        'translator_subtitle' => 'Translate text between English, French, and Kinyarwanda',
        'source_language' => 'Source Language',
        'target_language' => 'Target Language',
        'enter_text' => 'Enter text to translate...',
        'translate_btn' => 'Translate',
        'translation_result' => 'Translation Result',
        'clear_btn' => 'Clear',
        'copy_btn' => 'Copy',
        'swap_languages' => 'Swap Languages',
        'username' => 'Username',
        'email' => 'Email',
        'password' => 'Password',
        'dont_have_account' => "Don't have an account?",
        'register_as_buyer' => 'Register as Buyer',
        'required_field' => 'Required field',
        'invalid_credentials' => 'Invalid username or password',
        'login_successful' => 'Login successful! Redirecting...',
        'buyer_dashboard' => 'Buyer Dashboard',
        'seller_dashboard' => 'Seller Dashboard',
        'logout' => 'Logout',
        'currency' => 'FRW'
    ),
    'fr' => array(
        'site_title' => 'Système de Commerce International',
        'logo_text' => 'Commerce International',
        'welcome_message' => 'Bienvenue sur Notre Plateforme de Commerce International',
        'hero_description' => 'Connectez acheteurs et vendeurs à travers les langues avec notre système innovant de support linguistique local.',
        'featured_products' => 'Produits en Vedette',
        'search_placeholder' => 'Rechercher des produits...',
        'contact_us' => 'Contactez-nous',
        'home' => 'Accueil',
        'register' => 'S\'inscrire',
        'login' => 'Se connecter',
        'current_language' => 'Langue Actuelle',
        'choose_language' => 'Choisir la Langue',
        'translator' => 'Traducteur',
        'page_title' => 'Traducteur de Langues',
        'translator_title' => 'Traducteur Multi-Langues',
        'translator_subtitle' => 'Traduire du texte entre l\'anglais, le français et le kinyarwanda',
        'source_language' => 'Langue Source',
        'target_language' => 'Langue Cible',
        'enter_text' => 'Entrez le texte à traduire...',
        'translate_btn' => 'Traduire',
        'translation_result' => 'Résultat de la Traduction',
        'clear_btn' => 'Effacer',
        'copy_btn' => 'Copier',
        'swap_languages' => 'Échanger les Langues',
        'username' => 'Nom d\'utilisateur',
        'email' => 'Email',
        'password' => 'Mot de passe',
        'dont_have_account' => "Vous n'avez pas de compte?",
        'register_as_buyer' => 'S\'inscrire comme Acheteur',
        'required_field' => 'Champ requis',
        'invalid_credentials' => 'Nom d\'utilisateur ou mot de passe invalide',
        'login_successful' => 'Connexion réussie! Redirection...',
        'buyer_dashboard' => 'Tableau de Bord Acheteur',
        'seller_dashboard' => 'Tableau de Bord Vendeur',
        'logout' => 'Déconnexion',
        'currency' => 'FRW'
    ),
    'rw' => array(
        'site_title' => 'Sisitemu y\'Ubucuruzi Mpuzamahanga',
        'logo_text' => 'Ubucuruzi Mpuzamahanga',
        'welcome_message' => 'Murakaza neza kuri Sisitemu yacu y\'Ubucuruzi Mpuzamahanga',
        'hero_description' => 'Huza abaguzi n\'abacuruzi mu ndimi zitandukanye hamwe na sisitemu yacu igezweho yo gushyigikira indimi z\'aho.',
        'featured_products' => 'Ibicuruzwa Byibanze',
        'search_placeholder' => 'Shakisha ibicuruzwa...',
        'contact_us' => 'Twandikire',
        'home' => 'Ahabanza',
        'register' => 'Kwiyandikisha',
        'login' => 'Kwinjira',
        'current_language' => 'Ururimi Rukoresha',
        'choose_language' => 'Hitamo Ururimi',
        'translator' => 'Uburyo bwo Guhindura',
        'page_title' => 'Uburyo bwo Guhindura Indimi',
        'translator_title' => 'Sisitemu yo Guhindura Indimi Nyinshi',
        'translator_subtitle' => 'Hindura inyandiko hagati y\'Icyongereza, Igifaransa, n\'Ikinyarwanda',
        'source_language' => 'Ururimi Rutangirira',
        'target_language' => 'Ururimi Rugamije',
        'enter_text' => 'Andika inyandiko ugomba guhindura...',
        'translate_btn' => 'Hindura',
        'translation_result' => 'Igisubizo cy\'Ubuhindurzi',
        'clear_btn' => 'Siba',
        'copy_btn' => 'Kopi',
        'swap_languages' => 'Guhinduranya Indimi',
        'username' => 'Izina ry\'ukoresha',
        'email' => 'Imeyili',
        'password' => 'Ijambo ry\'ibanga',
        'dont_have_account' => "Ntufite konti?",
        'register_as_buyer' => 'Kwiyandikisha nk\'Umuguzi',
        'required_field' => 'Ikigomba cyuzuzwa',
        'invalid_credentials' => 'Izina ry\'ukoresha cyangwa ijambo ry\'ibanga sibyo',
        'login_successful' => 'Kwinjira byagenze neza! Urakurikiranwa...',
        'buyer_dashboard' => 'Imbonerahamwe y\'Umuguzi',
        'seller_dashboard' => 'Imbonerahamwe y\'Umucuruzi',
        'logout' => 'Gusohoka',
        'currency' => 'FRW'
    )
);

// Function to get translation
function t($key, $target_lang = null) {
    global $translations, $lang;
    $current_lang = $target_lang ?? $lang;
    return $translations[$current_lang][$key] ?? $translations['en'][$key] ?? $key;
}
?>











