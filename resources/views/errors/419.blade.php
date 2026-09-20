@include('errors.error-layout', [
    'code' => 419,
    'title' => 'Session expired',
    'message' => 'Your session has expired. Please go back and try again.',
    'back' => url()->previous(),
])