@include('errors.illustrated', [
    'status' => '419',
    'label' => 'SESSION EXPIRED',
    'title' => 'Срок действия сессии истёк',
    'description' => 'Страница оставалась неактивной слишком долго. Пожалуйста, обновите её и попробуйте снова.'
])
