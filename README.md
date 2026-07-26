# SprintMind-Ai 🚀
### AI-Powered Agile Scrum & Project Management Workspace

SprintMind-Ai is an advanced, modern project management workspace built on **Laravel 13** that integrates generative AI (**Google Gemini API**) to automate agile sprint planning, note analysis, task scheduling, and team productivity tracking.

---

## 📦 Core Features Developed & Uploaded to GitHub

This section lists the comprehensive features developed in the application, including architecture, models, views, and core AI capabilities.

### 1. Database Schema & Core Models Setup
> **Technical Comment:**
> Sets up the migration blueprints and Eloquent models for Sprints, Tasks, Acceptance Criteria, Notes, and Notifications. Includes dynamic casting for boolean flags and date structures, eager-loaded relationships between projects and sprints, and a `UserObserver` to automatically bootstrap default workspaces for new users.

### 2. Core AI Services (Google Gemini API Integration & Fallback Engine)
> **Technical Comment:**
> Implements the main API communications wrapper `AIAgileArchitectService` which interfaces with Gemini (`gemini-3.5-flash`). The service handles custom system prompts for structured JSON scaffolding, sprint target-velocity planning, note analysis/task extraction, calendar task auto-scheduling, and active sprint health check audits. Includes rule-based fallback engines to ensure zero downtime if the AI API is unreachable.

### 3. Project & Workspace Management
> **Technical Comment:**
> Implements full CRUD capabilities for project workspaces. Projects are categorized (`software`, `marketing`, or `personal`) and can be managed manually or generated as structured workspaces directly from an AI prompt.

### 4. Interactive Tasks Manager
> **Technical Comment:**
> Supports creation, updates, and deletion of project backlog items. Tasks support detailed descriptions, priority settings (high, medium, low), Fibonacci story points, start/due dates, and task-level checkbox lists for Acceptance Criteria.

### 5. Sprints & Kanban Boards
> **Technical Comment:**
> Manages sprint lifecycle transitions (planned, active, completed) and provides a drag-and-drop-capable Kanban Board interface. When a sprint is active, it tracks task state transitions (`Todo`, `In Progress`, `Code Review`, `Completed`) and runs real-time "Health Checks" to warn users of blockers, capacity overloads, or code review congestion with localized copilot advice.

### 6. Note Hub & AI Task Processor
> **Technical Comment:**
> Provides a rich markdown thoughts board with color coding, tag filtering, live search, and pin/unpin options. Includes a powerful AI feature that parses raw note content (such as meeting transcripts or outlines), extracts actionable stories, and previews them in a modal before committing them to the backlog.

### 7. Agile Calendar, Daily Capacity Heatmaps & AI Auto-Scheduler
> **Technical Comment:**
> Visualizes task schedules across an interactive calendar built with **FullCalendar.js**. Features a draggable backlog drawer, real-time daily capacity heatmaps (Green $\le 5$ SP, Amber $6-8$ SP, Red $> 8$ SP), and an AI auto-scheduling engine that distributes unscheduled backlog tasks across business weekdays based on deadlines, priorities, and velocity limits.

### 8. Web Routing & Global Dashboard UI
> **Technical Comment:**
> Configures all application endpoints in `routes/web.php` protected by `auth` and `verified` middlewares. The main dashboard uses statistical calculations to display completion progress wheels, total burned story points, task state distribution charts, and highlight immediate priority backlog items.

### 9. Notifications Hub & User Preferences
> **Technical Comment:**
> Provides an in-app notification center for tracking sprint updates, task completions, and urgent bottleneck alerts. Integrates user profile settings to customize notification channel preferences.

### 10. Global Search Engine
> **Technical Comment:**
> Implements unified global search across projects, backlog tasks, and notes with real-time filtering.

### 11. Automated Testing Suite (Pest PHP)
> **Technical Comment:**
> Built with **Pest PHP** feature and unit test coverage to verify authentication guards, Note CRUD operations, note pinning, AI task commits to backlog, notification hub management, and global search routes.

---

## 🛠️ Technology Stack

*   **Backend Framework:** Laravel 13 (PHP 8.3+)
*   **Frontend Engine:** Blade Templates, Tailwind CSS & Alpine.js
*   **Calendar & Interactivity:** FullCalendar.js & Material Symbols
*   **Database:** MySQL / SQLite
*   **AI Integration:** Google Gemini REST API (`gemini-3.5-flash`)
*   **Testing Suite:** Pest PHP

---

## ⚙️ Installation & Local Setup

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/mohammedjouda/SprintMind-Ai.git
    cd SprintMind-Ai
    ```
2.  **Install Dependencies:**
    ```bash
    composer install
    npm install && npm run build
    ```
3.  **Environment Configuration:**
    Create a local `.env` file from the template:
    ```bash
    cp .env.example .env
    ```
    Add your database credentials and Gemini API Key:
    ```env
    GEMINI_API_KEY="your_api_key_here"
    GEMINI_MODEL="gemini-3.5-flash"
    ```
4.  **Database Migration & Seeding:**
    ```bash
    php artisan migrate
    ```
5.  **Run Automated Tests:**
    ```bash
    php artisan test
    ```
6.  **Start Development Server:**
    ```bash
    php artisan serve
    ```


