<?php
// The main dashboard.php file provides $db_conn and $current_user_id
if (!isset($db_conn) || !isset($current_user_id)) {
    die("Error: This file should not be accessed directly.");
}
$sesion_model = new SesionApoyo($db_conn);
$sesiones = $sesion_model->readByUser($current_user_id)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="space-y-8">
    <h3 class="text-2xl font-heading font-semibold text-text-dark">Mis Sesiones de Apoyo</h3>

    <?php if (empty($sesiones)): ?>
        <div class="text-center py-12 border-2 border-dashed rounded-xl">
            <p class="text-gray-500">No has solicitado ninguna sesión de apoyo todavía.</p>
            <a href="apoyo-emocional.php" class="mt-4 inline-block bg-primary text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                Solicitar Apoyo
            </a>
        </div>
    <?php else: ?>
        <div class="divide-y divide-gray-200">
            <?php foreach ($sesiones as $s): ?>
                <div class="py-6">
                    <div class="flex justify-between items-center mb-2">
                        <p class="font-semibold text-primary capitalize"><?php echo htmlspecialchars($s['tipo_apoyo']); ?></p>
                        <p class="text-sm text-gray-500"><?php echo date('d M, Y', strtotime($s['fecha_sesion'])); ?></p>
                    </div>
                    <p class="text-gray-700 mb-4">
                        <span class="font-semibold">Tu consulta:</span> "<?php echo htmlspecialchars($s['descripcion_situacion']); ?>"
                    </p>
                    <?php if (!empty($s['consejo_brindado'])): ?>
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                            <p class="font-semibold text-green-800">Respuesta de nuestro equipo:</p>
                            <p class="text-gray-800 italic">"<?php echo htmlspecialchars($s['consejo_brindado']); ?>"</p>
                        </div>
                    <?php else: ?>
                         <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                            <p class="font-semibold text-yellow-800">Pendiente de respuesta</p>
                            <p class="text-gray-800">Nuestro equipo está revisando tu solicitud y se pondrá en contacto pronto.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
