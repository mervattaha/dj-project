<?php
try {
    $pdo = new PDO('sqlite:../src/database/cueup.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('INSERT INTO event_categories (name, slug) VALUES (:name, :slug)
                            ON CONFLICT(slug) DO UPDATE SET name=excluded.name');
    $stmt->execute([
        'name' => 'Corporate Events',
        'slug' => 'corporate_events'
    ]);

    $categoryId = $pdo->lastInsertId();

    $subcategories = [
        'conferences' => 'Conferences & Seminars',
        'product_launches' => 'Product Launches',
        'award_ceremonies' => 'Award Ceremonies',
        'trade_shows' => 'Trade Shows & Expos',
        'company_milestones' => 'Company Milestones',
        'team_building' => 'Team Building Events',
        'holiday_parties' => 'Holiday Parties',
        'networking_events' => 'Networking Events'
    ];

    foreach ($subcategories as $slug => $name) {
        $stmt = $pdo->prepare('INSERT INTO event_subcategories (category_id, name, slug) VALUES (:category_id, :name, :slug)
                                ON CONFLICT(slug) DO UPDATE SET name=excluded.name');
        $stmt->execute([
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug
        ]);
    }

    echo "Data updated successfully!";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
