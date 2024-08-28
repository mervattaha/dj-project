<?php
namespace App\Controllers;

use App\Controllers\MorePlacesController; // تأكد من تضمين MorePlacesController

class BaseController {
    protected $twig;
    protected $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    protected function renderWithFooter($template, $data = []) {
        $morePlacesController = new MorePlacesController($this->twig, $this->pdo);
        $userLocation = $this->getUserLocation();
        $cities = $morePlacesController->getNearbyCities($userLocation);

        // تسجيل رسالة خطأ في حال عدم وجود مدن قريبة
        if (empty($cities)) {
            error_log('No nearby cities available.'); // تسجيل رسالة الخطأ في سجل الأخطاء
            $cities = []; // تعيين مصفوفة فارغة لتجنب مشاكل العرض
        }

        echo $this->twig->render($template, array_merge($data, [
            'cities' => $cities,
        ]));
    }

    protected function getUserLocation() {
        // تأكد من أن إحداثيات الموقع صالحة إذا كان لديك نظام تحديد المواقع
        return ['latitude' => 30.0444, 'longitude' => 31.2357];
    }
}
