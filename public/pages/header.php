<header
    class="h-16 flex-shrink-0 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-10">
    <div class="flex items-center gap-4 flex-1 max-w-xl">
    </div>
    <div class="flex items-center gap-4">
        <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
        <div class="flex items-center gap-3 pl-2">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold leading-none">
                    <?php echo htmlspecialchars($user['name']); ?>
                </p>
                <p class="text-[10px] text-slate-500 font-medium uppercase mt-1">
                    <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                </p>
            </div>
            <div
                class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold border-2 border-primary/20">
                <?php
                $initials = '';
                $parts = explode(' ', $user['name']);
                foreach ($parts as $part) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
                echo substr($initials, 0, 2);
                ?>
            </div>
        </div>
    </div>
</header>