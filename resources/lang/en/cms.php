<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS — Feature Management (features/index)
    |--------------------------------------------------------------------------
    */

    'features' => [
        'title' => 'Feature Management',
        'card_title' => 'CMS Feature Management',
        'card_desc' => 'Manage all features displayed on the website',
        'add_button' => 'Add Feature',

        // Table headers
        'col_name' => 'Feature Name',
        'col_type' => 'Menu Type',
        'col_sub_count' => 'Sub Features',
        'col_order' => 'Order',
        'col_action' => 'Action',

        // Badges
        'type_dropdown' => 'Dropdown',
        'type_link' => 'Link',

        // Buttons
        'detail' => 'Detail',

        // Empty state
        'empty' => 'No features yet. Click "+ Add Feature" to create one.',

        // Edit modal
        'edit_title' => 'Edit Feature',

        // Add modal
        'add_title' => 'Add New Feature',

        // Delete modal
        'delete' => [
            'title' => 'Delete Feature',
            'confirm' => 'Are you sure you want to delete the feature :name? This action cannot be undone.',
            'yes' => 'Yes, Delete',
        ],

        // Form labels (shared between add/edit)
        'form' => [
            'name' => 'Feature Name',
            'type' => 'Menu Type',
            'path' => 'Path / URL',
            'path_placeholder' => 'Example: /home',
            'order' => 'Order',
            'name_placeholder' => 'Example: Home',
        ],

        // Detail page (features/show)
        'detail_title' => 'Feature Detail: :name',
        'type_label' => 'Type',

        // Sub-menu section (dropdown type)
        'sub' => [
            'list_title' => 'Sub Menu List — :name',
            'list_desc' => 'Manage sub menus within the :name menu',
            'add_button' => 'Add Sub Menu',
            'col_name' => 'Sub Menu Name',
            'col_path' => 'Path / URL',
            'col_order' => 'Order',
            'col_action' => 'Action',
            'empty' => 'No sub menus yet. Click "+ Add Sub Menu" to create one.',

            // Add sub modal
            'add_title' => 'Add Sub Menu',

            // Edit sub modal
            'edit_title' => 'Edit Sub Menu',

            // Delete sub modal
            'delete' => [
                'title' => 'Delete Sub Menu',
                'confirm' => 'Are you sure you want to delete the sub menu :name?',
                'yes' => 'Yes, Delete',
            ],

            // Sub form labels
            'form' => [
                'name' => 'Sub Menu Name',
                'path' => 'Path / URL',
                'path_placeholder' => 'Example: /profile/history',
                'name_placeholder' => 'Example: History',
                'order' => 'Order',
            ],
        ],

        // Content editor (link type)
        'content' => [
            'title' => 'Page Content Editor — :name',
            'desc' => 'Edit the content displayed on the :name page',
            'label' => 'Page Content',
            'placeholder' => 'Enter HTML or text content for this page...',
            'help' => 'You can use HTML to format the content.',
        ],

        // Flash messages
        'flash' => [
            'sub_added' => 'Sub menu added successfully.',
            'feature_added' => 'Feature added successfully.',
            'feature_updated' => 'Feature updated successfully.',
            'content_saved' => 'Page content saved successfully.',
            'feature_deleted' => 'Feature deleted successfully.',
            'sub_updated' => 'Sub feature updated successfully.',
            'sub_deleted' => 'Sub feature deleted successfully.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Feature Pages
    |--------------------------------------------------------------------------
    */

    'feature_pages' => [
        'title' => 'Page Management — :name',
        'desc' => 'Manage pages displayed for the :name feature',
        'add_button' => 'Add Page',
        'back_to_feature' => 'Back to Feature',

        'col_title' => 'Page Title',
        'col_sections' => 'Sections',
        'col_order' => 'Order',
        'col_action' => 'Action',

        'empty' => 'No pages yet. Click "+ Add Page" to create one.',

        'add_title' => 'Add New Page',
        'edit_title' => 'Edit Page',

        'delete' => [
            'title' => 'Delete Page',
            'confirm' => 'Are you sure you want to delete the page :name?',
            'yes' => 'Yes, Delete',
        ],

        'form' => [
            'title' => 'Page Title',
            'title_placeholder' => 'Example: Contemporary Exhibition',
            'description' => 'Page Description',
            'description_placeholder' => 'Brief description of this page...',
            'order' => 'Order',
        ],

        // Sections
        'sections_title' => 'Page Sections — :name',
        'sections_desc' => 'Manage content sections on the :name page',
        'add_section' => 'Add Section',
        'add_section_title' => 'Add New Section',
        'edit_section_title' => 'Edit Section',

        'section_form' => [
            'title' => 'Section Title',
            'title_placeholder' => 'Example: Mini Diorama Facility',
            'description' => 'Description',
            'description_placeholder' => 'Section description...',
            'images' => 'Images',
            'images_help' => 'Upload JPG/PNG/WebP images, max 2MB per file',
            'existing_images' => 'Current Images',
            'order' => 'Order',
        ],

        'delete_section' => [
            'title' => 'Delete Section',
            'confirm' => 'Are you sure you want to delete the section :name?',
            'yes' => 'Yes, Delete',
        ],

        'flash' => [
            'page_added' => 'Page added successfully.',
            'page_updated' => 'Page updated successfully.',
            'page_deleted' => 'Page deleted successfully.',
            'section_added' => 'Section added successfully.',
            'section_updated' => 'Section updated successfully.',
            'section_deleted' => 'Section deleted successfully.',
        ],

        // Public page
        'welcome' => 'Welcome to the :name portal,',
        'search_placeholder' => 'Search',
        'list_title' => ':name List',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Home Editor (home/edit)
    |--------------------------------------------------------------------------
    */

    'home' => [
        'title' => 'Home Page Content Editor',
        'desc' => 'Manage all content displayed on the Home page of the website',
        'view_page' => 'View Page',

        'hero' => [
            'title' => 'Hero Section (Main Banner)',
            'desc' => 'Main text and CTA button at the top of the page',
            'hero_title' => 'Hero Title',
            'hero_cta' => 'CTA Button Text',
        ],

        'feature_strip' => [
            'title' => 'Feature Strip (Below Hero Banner)',
            'desc' => 'Two information boxes below the hero',
            'left' => 'Left Text',
            'middle' => 'Middle Button',
            'right_button' => 'Right Button',
            'right_text' => 'Right Text',
        ],

        'info' => [
            'title' => 'DABB Information Section',
            'desc' => 'Title and two paragraphs of information about DABB',
            'section' => 'Section Title',
            'paragraph1' => 'Paragraph 1',
            'paragraph2' => 'Paragraph 2',
        ],

        'activities' => [
            'title' => 'Archival Activities Section',
            'desc' => '6 activity items displayed in colored cards',
            'section' => 'Section Title',
        ],

        'section_titles' => [
            'title' => 'Other Section Titles',
            'desc' => 'Titles for Gallery, Statistics, YouTube, Instagram sections, etc.',
            'related' => 'Related Links',
            'gallery' => 'Archive Exhibition (Gallery)',
            'stats' => 'Visitor Statistics',
            'youtube' => 'YouTube',
            'instagram' => 'Instagram Feed',
        ],

        'stats' => [
            'title' => 'Statistics Labels',
            'desc' => 'Text labels for visitor statistics counters',
            'total' => 'Total Visitors Label',
            'today' => 'Today\'s Visitors Label',
        ],

        'youtube' => [
            'title' => 'YouTube Videos',
            'desc' => 'YouTube video IDs displayed in the carousel (format: ID only, example: F2NhNTiNxoY)',
            'video_label' => 'Video :number',
            'placeholder' => 'YouTube ID',
            'help' => 'Copy the ID from the YouTube URL: youtube.com/watch?v=<strong>ID_HERE</strong>',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Virtual Rooms 360° (virtual_rooms)
    |--------------------------------------------------------------------------
    */

    'virtual_rooms' => [
        'breadcrumb_parent' => 'CMS / Virtual Exhibition Real',
        'breadcrumb_active' => 'Dashboard',
        'breadcrumb_form_parent' => 'CMS / Virtual Exhibition Real / Room List',
        'breadcrumb_edit' => 'Edit Room',
        'breadcrumb_create' => 'Add Room',

        'page_title' => 'Page Management &mdash; :name',
        'page_desc' => 'Manage virtual rooms and navigation hotspots for :name 360 degrees',
        'view_exhibition' => 'View Virtual Exhibition',
        'add_room' => 'Add Virtual Room',

        'stat_total_rooms' => 'Total Rooms',
        'stat_total_rooms_sub' => 'Active virtual rooms',
        'stat_total_hotspots' => 'Total Hotspots',
        'stat_total_hotspots_sub' => 'Active navigation points',
        'stat_avg_hotspots' => 'Average Hotspots',
        'stat_avg_hotspots_sub' => 'Per room',

        'table_title' => 'Virtual Room List',
        'col_no' => 'No',
        'col_thumbnail' => 'Thumbnail',
        'col_name' => 'Room Name',
        'col_desc' => 'Description',
        'col_hotspot' => 'Hotspot',
        'col_action' => 'Action',
        'empty' => 'No virtual rooms have been added yet.',
        'delete_confirm' => 'Are you sure you want to delete this room?',

        // Form (create/edit)
        'form_title_create' => 'Add Virtual Room',
        'form_title_edit' => 'Edit Virtual Room',
        'form_desc' => 'Update room information and configure navigation hotspots',
        'back_to_list' => 'Back to Room List',
        'info_title' => 'Room Information',
        'label_name' => 'Room Name',
        'label_desc' => 'Description',
        'label_thumbnail' => 'Room Thumbnail',
        'thumbnail_help' => 'Preview image for room list (JPG, PNG, WEBP)',
        'label_image_360' => '360° Image',
        'image_360_help' => 'Equirectangular 360 degree image (JPG, PNG)',

        'hotspot_title' => 'Navigation Hotspots',
        'hotspot_add' => 'Add',
        'hotspot_rooms_available' => 'Available rooms: :count',
        'hotspot_empty' => "Empty. Click 'Add'",

        'preview_title' => '360° Panorama Preview',
        'preview_desc' => 'Click a target point on the panorama to get Yaw/Pitch, or drag to look around',
        'preview_placeholder' => 'Preview not available',
        'preview_placeholder_sub' => 'Select a 360° image first',

        'btn_cancel' => 'Cancel',
        'btn_save' => 'Save Changes',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS — Virtual 3D Rooms (virtual_3d_rooms)
    |--------------------------------------------------------------------------
    */

    'virtual_3d_rooms' => [
        'breadcrumb_parent' => 'CMS / Virtual 3D Rooms',
        'breadcrumb_edit' => 'Edit: :name',
        'breadcrumb_create' => 'Add Room',

        'page_title' => 'Virtual 3D Rooms &mdash; :name',
        'page_desc' => 'Manage virtual rooms with 4 walls and interactive doors',
        'view_exhibition' => 'View Virtual Exhibition',
        'add_room' => 'Add 3D Room',

        'stat_total_rooms' => 'Total Rooms',
        'stat_total_rooms_sub' => 'Active virtual 3D rooms',
        'stat_total_media' => 'Total Media',
        'stat_total_media_sub' => 'Images &amp; videos on walls',
        'stat_avg_media' => 'Average Media',
        'stat_avg_media_sub' => 'Per room',

        'table_title' => 'Virtual 3D Room List',
        'col_no' => 'No',
        'col_thumbnail' => 'Thumbnail',
        'col_name' => 'Room Name',
        'col_desc' => 'Description',
        'col_media' => 'Media',
        'col_action' => 'Action',
        'empty' => 'No virtual 3D rooms have been added yet.',
        'delete_confirm' => 'Are you sure you want to delete this room? All wall media will also be deleted.',

        // Create form
        'form_title_create' => 'Add Virtual 3D Room',
        'form_desc_create' => 'Set up room information, wall/floor/ceiling colors, and navigation hotspots',
        'back_to_list' => 'Back to Room List',

        // Edit form
        'form_title_edit' => 'Edit Room: :name',
        'form_desc_edit' => 'Set up room information, colors, wall media, and navigation hotspots',

        // Shared form
        'info_title' => 'Room Information',
        'label_name' => 'Room Name',
        'label_desc' => 'Description',
        'label_thumbnail' => 'Room Thumbnail',
        'thumbnail_help' => 'Preview image for room list (JPG, PNG, WEBP)',
        'thumbnail_keep' => 'Leave empty if you don\'t want to change it',

        'colors_title' => 'Room Colors',
        'label_wall_color' => 'Wall Color',
        'label_floor_color' => 'Floor Color',
        'label_ceiling_color' => 'Ceiling Color',

        'door_title' => 'Door / Hotspot Settings',
        'door_desc' => 'The door is on the back wall of the 3D room and can direct visitors to another page or room.',
        'door_desc_edit' => 'Back wall door for navigation to other pages/rooms',
        'label_door_type' => 'Door Link Type',
        'door_type_none' => 'Inactive (Visual Only)',
        'door_type_room' => 'Navigate to Another Room',
        'door_type_url' => 'Free Link (URL)',
        'label_target_room' => 'Target Room',
        'target_room_placeholder' => '— Select Room —',
        'rooms_available' => 'Available rooms: :count',
        'label_target_url' => 'Target URL',
        'label_door_label' => 'Door Label (Optional)',
        'door_label_placeholder' => 'Example: EXIT',

        'media_title' => 'Wall Media (Photo / Video)',
        'media_save_first' => 'Save the room first',
        'media_save_first_sub' => 'After saving, you will be redirected to the edit page to add photos/videos to the room walls.',
        'media_items' => ':count items',
        'media_selected_wall' => 'Selected Wall',
        'media_wall_front' => 'Front Wall',
        'media_wall_hint' => 'Select a wall in the <strong>Media Position Editor</strong> panel on the right',
        'media_type_label' => 'Media Type',
        'media_type_image' => 'Image (JPG/PNG)',
        'media_type_video' => 'Video (MP4)',
        'media_file_label' => 'File Upload',
        'media_upload_btn' => 'Upload &amp; Add to Wall',
        'media_wall_label' => 'Wall: :wall',
        'media_delete' => 'Delete',
        'media_empty' => 'No media yet. Upload a file above.',
        'media_upload_success' => 'Media uploaded successfully!',
        'media_upload_choose' => 'Select a file to upload!',

        'preview_title' => '3D Room Preview',
        'preview_desc' => 'Live 3D room preview based on your color settings',
        'preview_desc_edit' => 'Live room preview based on your color settings',
        'preview_front' => 'FRONT',
        'preview_back' => 'BACK',
        'preview_left' => 'LEFT',
        'preview_right' => 'RIGHT',
        'preview_floor' => 'FLOOR',
        'preview_ceiling' => 'CEILING',
        'preview_door' => 'DOOR',
        'preview_btn_default' => 'Default',
        'preview_btn_front' => 'Front',
        'preview_btn_left' => 'Left',
        'preview_btn_right' => 'Right',
        'preview_btn_back' => 'Back',
        'preview_btn_top' => 'Top',

        'editor_title' => 'Wall Media Position Editor',
        'editor_desc' => 'Drag media to adjust position on the wall. Click media to show properties.',
        'editor_wall_front' => 'Front Wall',
        'editor_wall_left' => 'Left Wall',
        'editor_wall_right' => 'Right Wall',
        'editor_wall_back' => 'Back Wall',
        'editor_wall_title_front' => 'FRONT WALL',
        'editor_props_title' => 'Selected Media Properties',
        'editor_props_delete' => 'Delete',
        'editor_props_save' => 'Save Position',

        'btn_cancel' => 'Cancel',
        'btn_save_create' => 'Save Room',
        'btn_save_edit' => 'Save Changes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Common (shared across CMS pages)
    |--------------------------------------------------------------------------
    */

    'common' => [
        'cancel' => 'Cancel',
        'save_changes' => 'Save Changes',
        'save_content' => 'Save Content',
        'back' => 'Back',
        'required' => '*',
        'saved_successfully' => 'Settings saved successfully.',
    ],

];
