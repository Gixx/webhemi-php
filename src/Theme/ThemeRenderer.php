<?php

declare(strict_types=1);

namespace App\Theme;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Renders a template from a resolved theme package.
 */
final class ThemeRenderer
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function render(ThemePackage $theme, string $template, array $context = []): string
    {
        if ($theme->isShipped()) {
            return $this->twig->render('themes/' . $theme->id . '/' . $template, $context);
        }

        $loader = new FilesystemLoader($theme->templatesPath);
        $env = new Environment($loader, [
            'autoescape' => 'html',
            'strict_variables' => true,
        ]);

        return $env->render($template, $context);
    }
}
