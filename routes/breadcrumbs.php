<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.

// Home
Breadcrumbs::for('projects', function (BreadcrumbTrail $trail) {
    $trail->push('Accueil', route('projects.index'));
});

// Home > Projects > Project
Breadcrumbs::for('project', function (BreadcrumbTrail $trail, $project) {
    $trail->parent('projects');
    $trail->push($project->title, route('projects.show', $project));
});

// Home > Projects > Archives
Breadcrumbs::for('archives', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Archives', route('projects.archive'));
});

