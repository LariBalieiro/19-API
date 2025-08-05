<?php
if (PHP_SAPI !== 'cli') {
    exit('Rodar via CLI');
}

require __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;
use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;

// Configurações (adapte como quiser)
$settings = new Settings([
    'displayErrorDetails' => true,
    'logger' => [
        'name' => 'slim-app',
        'path' => __DIR__ . '/logs/app.log',
        'level' => \Monolog\Logger::DEBUG,
    ],
    'db' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'slim',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ],
]);

$containerBuilder = new ContainerBuilder();

// Registra o objeto Settings como implementação de SettingsInterface
$containerBuilder->addDefinitions([
    SettingsInterface::class => $settings,
]);

// ✅ Correção aqui — executa a função retornada por dependencies.php
(require __DIR__ . '/app/dependencies.php')($containerBuilder);

$container = $containerBuilder->build();

$db = $container->get('db');

$schema = $db->schema();
$table = 'produtos';

$schema->dropIfExists($table);

$schema->create($table, function ($table) {
    $table->increments('id');
    $table->string('titulo', 100);
    $table->text('descricao');
    $table->decimal('preco', 11, 2);
    $table->string('fabricante', 60);
    $table->timestamps();
});

// Preencher a tabela
$db->table($table)->insert([
    'titulo' => 'Smartphone Motorola Moto G6 32GB Dual Chip',
    'descricao' => 'Android Oreo - 8.0 Tela 5.7" Octa-Core 1.8 GHz 4G Câmera 12 + 5MP (Dual Traseira) - Índigo',
    'preco' => 899.00,
    'fabricante' => 'Mototola',
    'created_at' => '2019-10-22',
    'updated_at' => '2019-10-22'
]);

$db->table($table)->insert([
    'titulo' => 'iPhone X Cinza Espacial 64GB',
    'descricao' => 'Tela 5.8" IOS 12 4G Wi-fi Câmera 12MP - Apple',
    'preco' => 4999.00,
    'fabricante' => 'Apple',
    'created_at' => '2020-01-10',
    'updated_at' => '2020-01-10'
]);
