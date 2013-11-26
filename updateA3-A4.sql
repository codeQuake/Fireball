ALTER TABLE cms1_page ADD sidebarOrientation ENUM('left', 'right') NOT NULL DEFAULT 'right' AFTER showSidebar;

ALTER TABLE cms1_content ADD position ENUM('body', 'sidebar') NOT NULL DEFAULT 'body' AFTER cssClasses; 