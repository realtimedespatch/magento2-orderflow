<?php
/**
 * PHPUnit bootstrap for running this module from either dev/ or vendor/.
 */

declare(strict_types=1);

/**
 * Resolve the Magento project root by walking upward until the application bootstrap is found.
 */
$resolveMagentoRoot = static function (string $startDirectory): string {
    $directory = $startDirectory;

    while ($directory !== dirname($directory)) {
        $autoloadPath = $directory . '/app/autoload.php';
        $registrationPath = $directory . '/app/etc/NonComposerComponentRegistration.php';

        if (is_readable($autoloadPath) && is_readable($registrationPath)) {
            return $directory;
        }

        $directory = dirname($directory);
    }

    throw new RuntimeException('Unable to locate the Magento project root from the module test bootstrap.');
};

$magentoRoot = $resolveMagentoRoot(__DIR__);

require_once $magentoRoot . '/app/autoload.php';

if (!defined('TESTS_TEMP_DIR')) {
    $testsTempDir = '/var/tmp/orderflow-unit';

    if (!is_dir($testsTempDir) && !mkdir($testsTempDir, 0777, true) && !is_dir($testsTempDir)) {
        throw new RuntimeException(sprintf('Unable to create the PHPUnit temp directory at "%s".', $testsTempDir));
    }

    define('TESTS_TEMP_DIR', $testsTempDir);
}

// PHP 8 compatibility. Define constants that are not present in PHP < 8.0.
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 80000) {
    if (!defined('T_NAME_QUALIFIED')) {
        define('T_NAME_QUALIFIED', 24001);
    }
    if (!defined('T_NAME_FULLY_QUALIFIED')) {
        define('T_NAME_FULLY_QUALIFIED', 24002);
    }
}

require_once $magentoRoot . '/vendor/magento/magento2-base/dev/tests/unit/framework/autoload.php';

\Magento\Framework\Phrase::setRenderer(new \Magento\Framework\Phrase\Renderer\Placeholder());
