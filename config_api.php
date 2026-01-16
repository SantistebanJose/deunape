<?php
// config_api.php
// Archivo de configuración para la API de Decolecta

// Token de API - IMPORTANTE: Este archivo NO debe ser accesible públicamente
// Agregar este archivo a .gitignore si usas control de versiones
define('DECOLECTA_API_TOKEN', 'sk_12615.MFOiDGBYCNjlMiYkkHCr5ZrXUsHDJ5Qf');

// URLs base de la API
define('DECOLECTA_API_DNI', 'https://api.decolecta.com/v1/reniec/dni');
define('DECOLECTA_API_RUC', 'https://api.decolecta.com/v1/sunat/ruc/full');
?>