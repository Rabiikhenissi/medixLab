<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Guided onboarding tours
    |--------------------------------------------------------------------------
    |
    | Each authenticated role has a list of steps shown the first time the
    | user lands on their dashboard. A step highlights a target element with a
    | dark overlay (spotlight), keeps everything else unclickable and shows a
    | tooltip. Steps whose target is missing on the current page are skipped.
    |
    | Step shape:
    |   'target'   => string|array|null  CSS selector(s), first match wins, null = centered card
    |   'title'    => translation key    tooltip title
    |   'text'     => translation key    tooltip body
    |   'action'   => ['type' => 'input'|'click'|'submit']   wait for the user to perform
    |               the action before advancing (a "Next" button always remains)
    |   'placement'=> 'bottom'|'top'|'center'  tooltip position
    |
    */

    'roles' => [
        'doctor' => [
            'welcome' => [
                'target' => null,
                'title' => 'tour.doctor.welcome_title',
                'text' => 'tour.doctor.welcome',
            ],
            'search' => [
                'target' => '#header_patient_code',
                'title' => 'tour.doctor.search_title',
                'text' => 'tour.doctor.search',
                'action' => ['type' => 'input'],
            ],
            'prescribe' => [
                'target' => [
                    'a[href*="exams-selection"]',
                    'a.doctor-sidebar-item[title="Rechercher Patient"]',
                ],
                'title' => 'tour.doctor.prescribe_title',
                'text' => 'tour.doctor.prescribe',
            ],
            'patients' => [
                'target' => 'a.doctor-sidebar-item[title="Mes Patients"]',
                'title' => 'tour.doctor.patients_title',
                'text' => 'tour.doctor.patients',
            ],
            'exam_groups' => [
                'target' => 'a.doctor-sidebar-item[title="Groupes d\'Examens"]',
                'title' => 'tour.doctor.exam_groups_title',
                'text' => 'tour.doctor.exam_groups',
            ],
        ],

        'admin' => [
            'welcome' => [
                'target' => null,
                'title' => 'tour.admin.welcome_title',
                'text' => 'tour.admin.welcome',
            ],
            'users' => [
                'target' => 'a.sidebar-item[href*="/admin/users"]',
                'title' => 'tour.admin.users_title',
                'text' => 'tour.admin.users',
            ],
            'invite' => [
                'target' => [
                    'a[href*="/admin/users/invite"]',
                    'a.sidebar-item[href*="/admin/users"]',
                ],
                'title' => 'tour.admin.invite_title',
                'text' => 'tour.admin.invite',
            ],
            'gdpr' => [
                'target' => 'a.sidebar-item[href*="/admin/gdpr"]',
                'title' => 'tour.admin.gdpr_title',
                'text' => 'tour.admin.gdpr',
            ],
            'activity' => [
                'target' => 'a.sidebar-item[href*="/admin/activity"]',
                'title' => 'tour.admin.activity_title',
                'text' => 'tour.admin.activity',
            ],
        ],

        'center' => [
            'welcome' => [
                'target' => null,
                'title' => 'tour.center.welcome_title',
                'text' => 'tour.center.welcome',
            ],
            'exam_requests' => [
                'target' => 'a.center-sidebar-item[href*="/center/exam-requests"]',
                'title' => 'tour.center.exam_requests_title',
                'text' => 'tour.center.exam_requests',
            ],
            'samples' => [
                'target' => 'a.center-sidebar-item[href*="/center/samples"]',
                'title' => 'tour.center.samples_title',
                'text' => 'tour.center.samples',
            ],
            'scan' => [
                'target' => [
                    'a[href*="/center/samples/scan"]',
                    'a.center-sidebar-item[href*="/center/samples"]',
                ],
                'title' => 'tour.center.scan_title',
                'text' => 'tour.center.scan',
            ],
            'results' => [
                'target' => [
                    'a.center-sidebar-item[href*="results"]',
                    'a.center-sidebar-item[href*="/center/exam-requests"]',
                ],
                'title' => 'tour.center.results_title',
                'text' => 'tour.center.results',
            ],
            'billing' => [
                'target' => [
                    'a.center-sidebar-item[href*="/center/billing"]',
                    'a.center-sidebar-item[href*="/center/exam-requests"]',
                ],
                'title' => 'tour.center.billing_title',
                'text' => 'tour.center.billing',
            ],
        ],

        'patient' => [
            'welcome' => [
                'target' => null,
                'title' => 'tour.patient.welcome_title',
                'text' => 'tour.patient.welcome',
            ],
            'notifications' => [
                'target' => '#notificationBell',
                'title' => 'tour.patient.notifications_title',
                'text' => 'tour.patient.notifications',
            ],
            'scan' => [
                'target' => '#scanDoctorBtn',
                'title' => 'tour.patient.scan_title',
                'text' => 'tour.patient.scan',
            ],
            'access' => [
                'target' => '#accessRequestsList',
                'title' => 'tour.patient.access_title',
                'text' => 'tour.patient.access',
            ],
            'exams' => [
                'target' => '#examRequestsList',
                'title' => 'tour.patient.exams_title',
                'text' => 'tour.patient.exams',
            ],
        ],
    ],

];
