<?php

/** @var array $reception */
/** @var array $envoyes */
/** @var array $destinataires */
/** @var string $csrfToken */
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">Messages</h1>
</div>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="bg-red-500/15 border border-red-400/30 text-red-200 text-sm px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="bg-green-500/15 border border-green-400/30 text-green-200 text-sm px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<!-- Nouveau message -->
<div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6 mb-6">
    <h2 class="font-semibold text-white mb-4">Nouveau message</h2>
    <form method="POST" action="<?= BASE_URL ?>/messages" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <div>
            <label class="block text-sm font-medium text-white/70 mb-1">Destinataire</label>
            <select name="recipient_id" class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                <option value="" class="text-gray-800">Tout le monde</option>
                <?php foreach ($destinataires as $u): ?>
                    <option value="<?= htmlspecialchars($u['id']) ?>" class="text-gray-800">
                        <?= htmlspecialchars($u['name']) ?> (<?= $u['role'] === 'admin' ? 'Administrateur' : 'Vendeur' ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-white/70 mb-1">Message</label>
            <textarea name="content" required rows="3"
                class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2"></textarea>
        </div>
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded transition">
            Envoyer
        </button>
    </form>
</div>

<div class="grid lg:grid-cols-2 gap-6">

    <!-- Boîte de réception -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6">
        <h2 class="font-semibold text-white mb-4">Reçus (<?= count($reception) ?>)</h2>
        <?php if (empty($reception)): ?>
            <p class="text-white/40 text-sm">Aucun message reçu pour l'instant.</p>
        <?php else: ?>
            <div class="space-y-3 max-h-[500px] overflow-y-auto">
                <?php foreach ($reception as $m): ?>
                    <div class="bg-white/5 border <?= $m['is_read'] ? 'border-white/10' : 'border-orange-400/40' ?> rounded-lg p-4">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-white font-medium">
                                <?= htmlspecialchars($m['sender_name']) ?>
                                <?php if ($m['recipient_id'] === null): ?>
                                    <span class="text-xs font-normal text-orange-300">→ tout le monde</span>
                                <?php endif; ?>
                            </p>
                            <?php if (!$m['is_read']): ?>
                                <span class="shrink-0 w-2 h-2 rounded-full bg-orange-400"></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-white/80 whitespace-pre-wrap"><?= htmlspecialchars($m['content']) ?></p>
                        <p class="text-xs text-white/40 mt-2"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($m['created_at']))) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Envoyés -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6">
        <h2 class="font-semibold text-white mb-4">Envoyés (<?= count($envoyes) ?>)</h2>
        <?php if (empty($envoyes)): ?>
            <p class="text-white/40 text-sm">Aucun message envoyé pour l'instant.</p>
        <?php else: ?>
            <div class="space-y-3 max-h-[500px] overflow-y-auto">
                <?php foreach ($envoyes as $m): ?>
                    <div class="bg-white/5 border border-white/10 rounded-lg p-4">
                        <p class="text-white font-medium mb-1">
                            À <?= $m['recipient_id'] === null ? 'tout le monde' : htmlspecialchars($m['recipient_name'] ?? '—') ?>
                        </p>
                        <p class="text-sm text-white/80 whitespace-pre-wrap"><?= htmlspecialchars($m['content']) ?></p>
                        <p class="text-xs text-white/40 mt-2"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($m['created_at']))) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            window.location.reload();
        }
    });
</script>