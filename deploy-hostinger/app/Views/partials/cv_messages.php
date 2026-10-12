<?php
$messages = [];

if ($text = session()->getFlashdata('success')) {
    $messages[] = [
        'type' => 'success',
        'title' => '¡Listo!',
        'message' => $text,
    ];
}

if ($text = session()->getFlashdata('error')) {
    $messages[] = [
        'type' => 'error',
        'title' => 'Atención',
        'message' => $text,
    ];
}

if ($errors = session()->getFlashdata('errors')) {
    $lista = is_array($errors) ? array_values($errors) : [$errors];
    $messages[] = [
        'type' => 'error',
        'title' => 'Revise los datos',
        'message' => implode("\n", $lista),
    ];
}

if (!empty($messages)) {
    session()->remove(['success', 'error', 'errors']);
}
?>
<?php if (!empty($messages)): ?>
<script type="application/json" id="cv-flash-data"><?= json_encode($messages, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
