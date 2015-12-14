<?php
/**
 * Created by PhpStorm.
 * User: Aleksey Stetsenko
 * Date: 12/14/2015
 * Time: 12:29 PM
 */

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;


class AngularAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $js = [
        'angular/angular.js',
        'angular-route/angular-route.js',
        'angular-strap/dist/angular-strap.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}