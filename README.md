# SprintMind-Ai 🚀
### AI-Powered Agile Scrum & Project Management Workspace

SprintMind-Ai is an advanced, modern project management workspace built on **Laravel** that integrates generative AI (**Google Gemini API**) to automate agile sprint planning, note analysis, task scheduling, and team productivity tracking.

---

## 📦 Latest Features Developed & Uploaded to GitHub

This section lists the latest features we've developed and uploaded to GitHub. Each feature has its own separate, clear comment detailing its architecture, models, views, and core capabilities.

### 1. Database Schema & Core Models Setup
> **Technical Comment:**
> Sets up the migration blueprints and Eloquent models for Sprints, Tasks, Acceptance Criteria, and Notes. Includes dynamic casting for boolean flags and date structures, eager-loaded relationships between projects and sprints, and a `UserObserver` to automatically bootstrap default workspaces for new users.

### 2. Core AI Services (Google Gemini API Integration)
> **Technical Comment:**
> Implements the main API communications wrapper `AIAgileArchitectService` which interfaces with Gemini `gemini-3.5-flash`. The service handles custom system prompts for structured JSON scaffolding, sprint target-velocity planning, note analysis/task extraction, calendar task auto-scheduling, and active sprint health check audits.

### 3. Project & Workspace Management
> **Technical Comment:**
> Implements full CRUD capabilities for project workspaces. Projects are categorized (e.g. `software`, `marketing`, or `personal`) and can be managed manually or generated as structured workspaces directly from an AI prompt.

### 4. Interactive Tasks Manager
> **Technical Comment:**
> Supports creation, updates, and deletion of project backlog items. Tasks support detailed descriptions, priority settings (high, medium, low), Fibonacci story points, and task-level checkbox lists for Acceptance Criteria.

### 5. Sprints & Kanban Boards
> **Technical Comment:**
> Manages sprint lifecycle transitions (planned, active, completed) and provides a drag-and-drop-capable Kanban Board interface. When a sprint is active, it tracks task state transitions and runs real-time "Health Checks" to warn users of blockers, capacity overloads, or scope creep.

### 6. Note Hub & AI Task Processor
> **Technical Comment:**
> Provides an unstructured thoughts board. Users can draft, save, and pin/unpin notes. Includes a powerful AI feature that parses raw note content (such as meeting transcripts or outlines), extracts actionable stories, and commits them to the backlog as ready-to-run tasks.

### 7. Agile Calendar & AI Auto-Scheduler
> **Technical Comment:**
> Visualizes task schedules across a responsive grid layout. Integrates an AI scheduling engine that distributes unscheduled backlog tasks across upcoming dates based on deadlines, task priorities, and developer velocity.

### 8. Web Routing & Global Dashboard UI
> **Technical Comment:**
> Configures all application endpoints in `routes/web.php` protected by auth middlewares. The main dashboard uses statistical calculations to display sprint progress bars, task state distribution charts, and highlight immediate priority backlog items.

---

## 🛠️ Technology Stack
*   **Backend Framework:** Laravel 11 (PHP)
*   **Frontend Engine:** Blade Templates & Vanilla CSS
*   **Database:** MySQL / SQLite
*   **AI Integration:** Google Gemini REST API

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
5.  **Start Development Server:**
    ```bash
    php artisan serve
    ```

