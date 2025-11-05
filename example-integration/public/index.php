<?php

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new OmniKassa\ExampleIntegration\Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
