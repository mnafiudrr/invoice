@include('errors.error-layout', [
    'code' => 403,
    'title' => 'Forbidden',
    'message' => 'You do not have permission to access this page.',
])