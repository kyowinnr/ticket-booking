<?php

use Illuminate\\Support\\Facades\\Artisan;

Artisan::command('app:about', function () {
    $this->info('布袋港－澎湖船票訂位系統');
})->purpose('Display application information');
