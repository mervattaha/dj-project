<?php
namespace App\Controllers;

use Twig\Environment;
use PDO;

class BaseController
{
    protected $pdo;
    protected $twig;

    public function __construct(PDO $pdo, Environment $twig)
    {
        $this->pdo = $pdo;
        $this->twig = $twig;
    }
    /**
     * Renders a template with a footer included.
     *
     * @param string $template The name of the template file
     * @param array $data Data to pass to the template
     * @return string Rendered template
     */
    public function renderWithFooter(string $template, array $data = []): string
    {
        // Render the main content
        $content = $this->twig->render($template, $data);

        // Render the footer template
        $footer = $this->twig->render('footer.twig', $data);

        // Combine main content and footer
        return $content . $footer;
    }
}
