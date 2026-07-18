
<style>

    /* Design System Tokens */
    :root {
      --font-title: 'Outfit', sans-serif;
      --font-body: 'Inter', sans-serif;
      
      /* Light Mode Colors */
      --bg: #f8fafc;
      --bg-card: #ffffff;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border: #e2e8f0;
      
      --primary: #4f46e5;
      --primary-hover: #4338ca;
      --primary-light: #e0e7ff;
      --primary-rgb: 79, 70, 229;
      
      --accent: #06b6d4;
      --accent-light: #ecfeff;
      
      --success: #10b981;
      --success-light: #d1fae5;
      
      --popular-glow: rgba(79, 70, 229, 0.15);
      
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
      --shadow-glow: 0 0 20px 0 rgba(79, 70, 229, 0.2);
    }

    html.dark .interconnected-pricing {
      /* Dark Mode Colors */
      --bg: #090d16;
      --bg-card: #111827;
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --border: #1e293b;
      
      --primary: #6366f1;
      --primary-hover: #4f46e5;
      --primary-light: #1e1b4b;
      --primary-rgb: 99, 102, 241;
      
      --accent: #22d3ee;
      --accent-light: #083344;
      
      --success: #34d399;
      --success-light: #064e3b;
      
      --popular-glow: rgba(99, 102, 241, 0.25);
      
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.4);
      --shadow-glow: 0 0 25px 0 rgba(99, 102, 241, 0.35);
    }

    /* Resets and Core Styles */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    .interconnected-pricing {
      font-family: var(--font-body);
      background-color: var(--bg);
      color: var(--text-main);
      line-height: 1.5;
      padding: 40px 0;
      transition: background-color 0.3s ease, color 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    /* Grid Background Pattern */
    .bg-grid {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 600px;
      background-size: 40px 40px;
      background-image: 
        linear-gradient(to right, var(--border) 1px, transparent 1px),
        linear-gradient(to bottom, var(--border) 1px, transparent 1px);
      mask-image: radial-gradient(ellipse 60% 50% at 50% 0%, #000 70%, transparent 100%);
      -webkit-mask-image: radial-gradient(ellipse 60% 50% at 50% 0%, #000 70%, transparent 100%);
      opacity: 0.4;
      pointer-events: none;
      z-index: 0;
    }

    .container {
      max-width: 1320px;
      margin: 0 auto;
      padding: 80px 24px;
      position: relative;
      z-index: 1;
    }

    /* Header styling */
    .header {
      text-align: center;
      margin-bottom: 50px;
    }

    .badge-promo {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 16px;
      background: var(--primary-light);
      color: var(--primary);
    }

    .title {
      font-family: var(--font-title);
      font-size: clamp(2rem, 4vw, 3.25rem);
      font-weight: 800;
      line-height: 1.2;
      letter-spacing: -0.02em;
      margin-bottom: 16px;
      background: linear-gradient(135deg, var(--text-main) 60%, var(--primary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .subtitle {
      font-size: clamp(1rem, 2vw, 1.25rem);
      color: var(--text-muted);
      max-width: 650px;
      margin: 0 auto 30px;
    }

    /* Navigation & Theme Toggle */
    .top-bar {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      margin-bottom: 20px;
      gap: 15px;
    }

    .theme-toggle-btn {
      background: var(--bg-card);
      border: 1px solid var(--border);
      color: var(--text-main);
      padding: 10px;
      border-radius: 9999px;
      cursor: pointer;
      box-shadow: var(--shadow-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
    }

    .theme-toggle-btn:hover {
      background: var(--border);
      transform: scale(1.05);
    }

    .theme-toggle-btn svg {
      width: 20px;
      height: 20px;
      fill: none;
      stroke: currentColor;
      stroke-width: 2;
    }

    /* Upgrade Hierarchy Visualizer */
    .hierarchy-container {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 30px;
      margin-bottom: 50px;
      box-shadow: var(--shadow-md);
    }

    .hierarchy-title {
      font-family: var(--font-title);
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .hierarchy-flow {
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      overflow-x: auto;
      padding: 15px 10px;
      gap: 10px;
      scrollbar-width: thin;
    }

    .hierarchy-flow::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 30px;
      right: 30px;
      height: 4px;
      background: var(--border);
      z-index: 1;
      transform: translateY(-50%);
    }

    .hierarchy-progress-bar {
      position: absolute;
      top: 50%;
      left: 30px;
      height: 4px;
      background: linear-gradient(to right, var(--primary), var(--accent));
      z-index: 2;
      transform: translateY(-50%);
      width: 0%;
      transition: width 0.4s ease;
    }

    .hierarchy-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      z-index: 3;
      cursor: pointer;
      min-width: 90px;
    }

    .hierarchy-node {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: var(--bg-card);
      border: 3px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.85rem;
      color: var(--text-muted);
      transition: all 0.3s ease;
      box-shadow: var(--shadow-sm);
    }

    .hierarchy-step:hover .hierarchy-node,
    .hierarchy-step.active .hierarchy-node {
      border-color: var(--primary);
      color: var(--primary);
      transform: scale(1.1);
      box-shadow: 0 0 12px rgba(79, 70, 229, 0.3);
    }

    .hierarchy-step.active-path .hierarchy-node {
      border-color: var(--primary);
      background: var(--primary);
      color: #ffffff;
    }

    .hierarchy-label {
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-muted);
      margin-top: 8px;
      text-align: center;
      white-space: nowrap;
      transition: color 0.3s ease;
    }

    .hierarchy-step:hover .hierarchy-label,
    .hierarchy-step.active .hierarchy-label {
      color: var(--text-main);
    }

    /* Upgrade Flow / Interactive Recommender */
    .recommender-card {
      background: linear-gradient(135deg, var(--bg-card) 60%, var(--primary-light));
      border: 1px solid var(--border);
      border-radius: 24px;
      padding: 35px;
      margin-bottom: 60px;
      box-shadow: var(--shadow-lg);
      position: relative;
      overflow: hidden;
    }

    .recommender-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 250px;
      height: 250px;
      background: radial-gradient(circle, var(--popular-glow) 0%, transparent 70%);
      pointer-events: none;
    }

    .recommender-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
    }

    @media (max-width: 768px) {
      .recommender-grid {
        grid-template-columns: 1fr;
      }
    }

    .recommender-inputs {
      display: flex;
      flex-direction: column;
      gap: 25px;
    }

    .input-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .input-label {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-main);
      display: flex;
      justify-content: space-between;
    }

    .input-label span.value-display {
      color: var(--primary);
      font-weight: 700;
    }

    /* Stylized Sliders */
    .slider {
      -webkit-appearance: none;
      width: 100%;
      height: 6px;
      border-radius: 9999px;
      background: var(--border);
      outline: none;
      transition: background 0.3s ease;
    }

    .slider::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: var(--primary);
      border: 3px solid #ffffff;
      cursor: pointer;
      box-shadow: var(--shadow-md);
      transition: transform 0.1s ease;
    }

    .slider::-webkit-slider-thumb:hover {
      transform: scale(1.15);
      background: var(--primary-hover);
    }

    /* Toggle switches */
    .toggle-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: rgba(0, 0, 0, 0.02);
      padding: 12px 16px;
      border-radius: 12px;
      border: 1px solid var(--border);
    }

    .toggle-text {
      display: flex;
      flex-direction: column;
    }

    .toggle-title {
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--text-main);
    }

    .toggle-desc {
      font-size: 0.75rem;
      color: var(--text-muted);
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 48px;
      height: 26px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider-round {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: var(--border);
      transition: .3s;
      border-radius: 34px;
    }

    .slider-round:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .3s;
      border-radius: 50%;
    }

    input:checked + .slider-round {
      background-color: var(--primary);
    }

    input:checked + .slider-round:before {
      transform: translateX(22px);
    }

    /* Output Card */
    .recommender-result {
      background: rgba(var(--primary-rgb), 0.04);
      border: 2px dashed var(--primary);
      border-radius: 20px;
      padding: 30px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      transition: all 0.3s ease;
    }

    .recommender-result.highlight-pulse {
      animation: pulse-glow 0.8s ease-in-out;
    }

    @keyframes pulse-glow {
      0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0.4); }
      50% { transform: scale(1.02); box-shadow: 0 0 15px 5px rgba(var(--primary-rgb), 0.2); }
      100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0); }
    }

    .result-badge {
      background: var(--primary);
      color: #ffffff;
      padding: 4px 12px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 700;
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .result-name {
      font-family: var(--font-title);
      font-size: 1.75rem;
      font-weight: 800;
      color: var(--text-main);
      margin-bottom: 5px;
    }

    .result-price {
      font-size: 2.25rem;
      font-weight: 800;
      color: var(--primary);
      margin-bottom: 15px;
    }

    .result-desc {
      font-size: 0.88rem;
      color: var(--text-muted);
      margin-bottom: 20px;
      max-width: 320px;
    }

    .result-action-btn {
      background: var(--primary);
      color: #ffffff;
      border: none;
      padding: 12px 30px;
      border-radius: 9999px;
      font-weight: 600;
      font-size: 0.95rem;
      cursor: pointer;
      box-shadow: var(--shadow-md);
      transition: all 0.2s ease;
      text-decoration: none;
    }

    .result-action-btn:hover {
      background: var(--primary-hover);
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    /* Pricing Cards Grid */
    .plans-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
      margin-bottom: 80px;
    }

    @media (max-width: 1200px) {
      .plans-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 640px) {
      .plans-grid {
        grid-template-columns: 1fr;
      }
    }

    /* Individual Card Style */
    .card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 30px;
      position: relative;
      display: flex;
      flex-direction: column;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: var(--shadow-md);
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-xl);
      border-color: var(--primary);
    }

    .card.highlighted {
      border: 2.5px solid var(--primary);
      box-shadow: var(--shadow-glow);
    }

    .card-badge {
      position: absolute;
      top: -12px;
      left: 30px;
      background: var(--accent);
      color: #ffffff;
      padding: 4px 12px;
      border-radius: 9999px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      box-shadow: var(--shadow-sm);
    }

    .card.highlighted .card-badge {
      background: linear-gradient(135deg, var(--primary), var(--accent));
    }

    .card-header {
      margin-bottom: 24px;
    }

    .card-name {
      font-family: var(--font-title);
      font-size: 1.35rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .card-price-container {
      display: flex;
      align-items: baseline;
      gap: 4px;
      margin-bottom: 12px;
    }

    .card-price {
      font-size: 2.25rem;
      font-weight: 800;
      letter-spacing: -0.02em;
    }

    .card-period {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .card-description {
      font-size: 0.85rem;
      color: var(--text-muted);
      min-height: 60px;
      margin-bottom: 10px;
    }

    /* Interconnected "Includes previous plan" divider */
    .card-inclusion-info {
      background: var(--primary-light);
      color: var(--primary);
      padding: 10px 14px;
      border-radius: 12px;
      font-size: 0.78rem;
      font-weight: 500;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .card-inclusion-info svg {
      width: 16px;
      height: 16px;
      flex-shrink: 0;
    }

    /* Feature Lists */
    .card-features-title {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      margin-bottom: 12px;
    }

    .card-features-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 30px;
      flex-grow: 1;
    }

    .card-feature-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 0.88rem;
      color: var(--text-main);
    }

    .card-feature-item.highlight {
      font-weight: 600;
    }

    .card-feature-icon {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--success-light);
      color: var(--success);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .card-feature-icon svg {
      width: 10px;
      height: 10px;
      fill: none;
      stroke: currentColor;
      stroke-width: 3;
    }

    .card-action-btn {
      width: 100%;
      border: 1px solid var(--border);
      background: var(--bg-card);
      color: var(--primary);
      padding: 12px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      text-decoration: none;
    }

    .card-action-btn:hover {
      background: var(--primary);
      color: #ffffff;
      border-color: var(--primary);
      box-shadow: var(--shadow-md);
    }

    .card.highlighted .card-action-btn {
      background: var(--primary);
      color: #ffffff;
      border-color: var(--primary);
      box-shadow: var(--shadow-md);
    }

    .card.highlighted .card-action-btn:hover {
      background: var(--primary-hover);
      box-shadow: var(--shadow-lg);
    }

    /* Comparison Section */
    .comparison-section {
      margin-top: 100px;
    }

    .comparison-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 30px;
    }

    @media (max-width: 768px) {
      .comparison-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
    }

    .comparison-title {
      font-family: var(--font-title);
      font-size: 1.75rem;
      font-weight: 800;
    }

    .comparison-toggle {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 9999px;
      padding: 4px;
      display: inline-flex;
      cursor: pointer;
    }

    .comparison-toggle-btn {
      padding: 8px 18px;
      border-radius: 9999px;
      font-size: 0.85rem;
      font-weight: 600;
      border: none;
      background: transparent;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .comparison-toggle-btn.active {
      background: var(--primary);
      color: #ffffff;
      box-shadow: var(--shadow-sm);
    }

    /* Comparison Table Styling */
    .table-container {
      width: 100%;
      overflow-x: auto;
      border: 1px solid var(--border);
      border-radius: 20px;
      background: var(--bg-card);
      box-shadow: var(--shadow-md);
    }

    .comparison-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.85rem;
      min-width: 1000px; /* Forces scroll on mobile */
    }

    .comparison-table th, 
    .comparison-table td {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
    }

    .comparison-table tr:hover {
      background: rgba(0, 0, 0, 0.01);
    }

    /* Sticky columns */
    .comparison-table th:first-child,
    .comparison-table td:first-child {
      position: sticky;
      left: 0;
      background: var(--bg-card);
      z-index: 10;
      width: 250px;
      font-weight: 600;
      border-right: 1px solid var(--border);
    }

    .comparison-table th {
      background: rgba(0, 0, 0, 0.02);
      font-family: var(--font-title);
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--text-main);
    }

    .comparison-table th.col-plan {
      text-align: center;
      width: 120px;
    }

    .comparison-table td.col-val {
      text-align: center;
    }

    /* Table Badges & Marks */
    .check-icon {
      color: var(--success);
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .check-icon svg {
      width: 16px;
      height: 16px;
      fill: none;
      stroke: currentColor;
      stroke-width: 3;
    }

    .cross-icon {
      color: var(--text-muted);
      opacity: 0.4;
    }

    .table-val-text {
      font-weight: 600;
      font-size: 0.8rem;
      padding: 4px 8px;
      border-radius: 6px;
      background: var(--bg);
      border: 1px solid var(--border);
      display: inline-block;
    }

    /* Category header row */
    .comparison-table tr.category-row {
      background: rgba(var(--primary-rgb), 0.03) !important;
    }

    .comparison-table tr.category-row td {
      font-family: var(--font-title);
      font-weight: 700;
      font-size: 0.9rem;
      color: var(--primary);
      letter-spacing: 0.02em;
      border-right: none !important;
    }

    /* Footer / Extra Info */
    .footer {
      text-align: center;
      margin-top: 80px;
      color: var(--text-muted);
      font-size: 0.88rem;
      border-top: 1px solid var(--border);
      padding-top: 40px;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 15px;
    }

    .footer-links a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }

    .footer-links a:hover {
      text-decoration: underline;
    }
  
</style>

<div class="interconnected-pricing">
  

  <div class="bg-grid"></div>

  <div class="container">
    
    <!-- Top actions & Theme Mode -->
    

    <!-- Header Section -->
    <div class="header">
      <h1 class="title">Our Packages</h1>
      <p class="subtitle">A fully interconnected billing structure. Upgrading unlocks additional premium capabilities while retaining 100% of your previous plan's benefits.</p>
    </div>

    <!-- Upgrade Hierarchy Visualizer -->
    <div class="hierarchy-container">
      <div class="hierarchy-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
        </svg>
        Interconnected Upgrade Path (Unlock benefits cumulatively)
      </div>
      <div class="hierarchy-flow" id="hierarchyFlow">
        <div class="hierarchy-progress-bar" id="hierarchyProgress"></div>
        <!-- Steps rendered dynamically by JS -->
      </div>
    </div>

    <!-- Interactive Recommender Flow -->
    <div class="recommender-card">
      <h2 class="hierarchy-title" style="margin-bottom: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9.663 17h4.673M12 3v1m6.364 1.364l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 01-2 2h0a2 2 0 01-2-2v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
        </svg>
        Interactive Plan Recommender
      </h2>
      <p class="subtitle" style="margin: 0 0 25px 0; text-align: left; font-size: 0.9rem;">Adjust your expected usage metrics to identify the absolute best value package for your event.</p>
      
      <div class="recommender-grid">
        <div class="recommender-inputs">
          
          <!-- Slider 1: Events -->
          <div class="input-group">
            <label class="input-label" for="recommenderEvents">
              <span>Expected Number of Events</span>
              <span class="value-display" id="eventsVal">1 Event</span>
            </label>
            <input type="range" class="slider" id="recommenderEvents" min="1" max="15" value="1">
          </div>

          <!-- Slider 2: Entries -->
          <div class="input-group">
            <label class="input-label" for="recommenderEntries">
              <span>Total Ledger Entries (Gifts/Chandla)</span>
              <span class="value-display" id="entriesVal">50 Entries</span>
            </label>
            <input type="range" class="slider" id="recommenderEntries" min="50" max="1000" step="50" value="50">
          </div>

          <!-- Toggle 1: QR Collections -->
          <div class="toggle-container">
            <div class="toggle-text">
              <span class="toggle-title">Personal UPI/QR Collections</span>
              <span class="toggle-desc">Guests scan to contribute directly to you</span>
            </div>
            <label class="switch">
              <input type="checkbox" id="recommenderQR">
              <span class="slider-round"></span>
            </label>
          </div>

          <!-- Toggle 2: Family Editors -->
          <div class="toggle-container">
            <div class="toggle-text">
              <span class="toggle-title">Multi-user Family Editors</span>
              <span class="toggle-desc">Enable multiple people to manage the ledger</span>
            </div>
            <label class="switch">
              <input type="checkbox" id="recommenderEditors">
              <span class="slider-round"></span>
            </label>
          </div>

        </div>

        <div class="recommender-result" id="recommendationResult">
          <!-- Populated by JS -->
        </div>
      </div>
    </div>

    <!-- Pricing Cards Grid -->
    <div class="plans-grid" id="plansGrid">
      <!-- Plans dynamically populated by JS -->
    </div>

    <!-- Features Comparison Section -->
    <div class="comparison-section" id="comparisonTableSection">
      <div class="comparison-header">
        <div>
          <h2 class="comparison-title">Detailed Feature Matrix</h2>
          <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">Compare exact features and scale parameters side-by-side.</p>
        </div>
        <div class="comparison-toggle" id="tableFilterToggle">
          <button class="comparison-toggle-btn active" data-filter="all">All Plans</button>
          <button class="comparison-toggle-btn" data-filter="popular">Popular Plans</button>
        </div>
      </div>

      <div class="table-container">
        <table class="comparison-table" id="comparisonTable">
          <!-- Generated dynamically by JS -->
        </table>
      </div>
    </div>



  </div>

  <!-- JavaScript logic to render pricing and handle UI interactions -->
  
</div>

<script>
    @php
        $currentUserPlanLevel = Auth::check() ? Auth::user()->planLevel() : 0;
        $guestPayCredits = Auth::check() ? (int) (Auth::user()->guest_pay_single_event_credits ?? 0) : 0;
    @endphp
    const currentUserPlanLevel = {{ $currentUserPlanLevel }};
    const guestPayCredits = {{ $guestPayCredits }};

    // Data model representing the interconnected pricing structure
    const PLANS = [
      {
        id: 'starter',
        tier: 0,
        name: 'Starter Plan',
        price: 0,
        formattedPrice: '₹0',
        period: 'Free Plan',
        description: 'Perfect for small, intimate family events and basic ledger management.',
        badge: 'Starter',
        isPopular: false,
        features: [
          { text: '1 Event Limit', highlight: false },
          { text: 'Up to 50 Gift/Chandla Entries', highlight: true },
          { text: 'Basic Ledger Management', highlight: false },
          { text: 'Standard PDF Export', highlight: false },
          { text: 'Cash & Cover Tracking', highlight: false },
          { text: '3 Family Viewers (Read Only)', highlight: false }
        ],
        limits: {
          events: 1,
          entries: 50,
          qrCollection: false,
          editors: 0
        }
      },
      {
        id: 'celebration',
        tier: 1,
        name: 'Celebration Pack',
        price: {{ config('packs.celebration.amount_inr', 300) }}, formattedPrice: '₹{{ number_format(config('packs.celebration.amount_inr', 300), 0) }}',
        period: 'One-Time Payment',
        description: 'Enhance your celebration with printable invitations and graphic studio assets.',
        badge: 'Best Value',
        isPopular: false,
        features: [
          { text: '10 Invitation Templates', highlight: true },
          { text: 'Printable Invitation Designs', highlight: false },
          { text: 'Event Story / Reel Video Creator', highlight: true },
          { text: 'Countdown Studio Access', highlight: false },
          { text: 'Event Graphics & Social Posts', highlight: false }
        ],
        limits: {
          events: 1,
          entries: 50,
          qrCollection: false,
          editors: 0
        }
      },
      {
        id: 'guest_contribution',
        tier: 2, // stackable
        name: 'Guest Contribution',
        price: {{ config('packs.guest_pay_single.amount_inr', 400) }}, formattedPrice: '₹{{ number_format(config('packs.guest_pay_single.amount_inr', 400), 0) }}',
        period: 'One-Time Payment',
        description: 'Direct payment collections and unlimited ledger entries for your single event.',
        badge: 'Recommended',
        isPopular: false,
        features: [
          { text: 'Personal UPI / QR Collection', highlight: true },
          { text: 'Guest Payment Tracking', highlight: false },
          { text: 'Unlimited Entries (1 Event)', highlight: true },
          { text: 'Collection Status Reports', highlight: false },
          { text: 'Full Event PDF', highlight: false }
        ],
        limits: {
          events: 1,
          entries: 999999,
          qrCollection: true,
          editors: 0
        }
      },
      {
        id: 'host_plus',
        tier: 3,
        name: 'Host Plus Plan',
        price: {{ config('packs.ledger_duo.amount_inr', 500) }}, formattedPrice: '₹{{ number_format(config('packs.ledger_duo.amount_inr', 500), 0) }}',
        period: 'One-Time Payment',
        description: 'Manage multiple events with unlimited ledger logs and hosting tools.',
        badge: 'Great Value',
        isPopular: false,
        features: [
          { text: 'Up to 2 Events', highlight: true },
          { text: 'Unlimited Entries (All Events)', highlight: true },
          { text: 'Advanced Financial Reports', highlight: false },
          { text: 'Full Event PDF Downloads', highlight: false },
          { text: 'Additional Hosting & Seating Tools', highlight: false }
        ],
        limits: {
          events: 2,
          entries: 999999,
          qrCollection: true,
          editors: 0
        }
      },
      {
        id: 'family',
        tier: 4,
        name: 'Family Plan',
        price: {{ config('packs.family.amount_inr', 600) }}, formattedPrice: '₹{{ number_format(config('packs.family.amount_inr', 600), 0) }}',
        period: 'One-Time Payment',
        description: 'Coordinate family functions together with multi-editor read/write accounts.',
        badge: 'Family Pick',
        isPopular: false,
        features: [
          { text: '3 Family Editors (Write Access)', highlight: true },
          { text: 'Shared Event Management Space', highlight: false },
          { text: 'Joint Family Hosting Support', highlight: false },
          { text: 'Role-Based Team Permissions', highlight: false }
        ],
        limits: {
          events: 2,
          entries: 999999,
          qrCollection: true,
          editors: 3
        }
      },
      {
        id: 'premium_host',
        tier: 5,
        name: 'Premium Host',
        price: {{ config('packs.premium_bundle.amount_inr', 700) }}, formattedPrice: '₹{{ number_format(config('packs.premium_bundle.amount_inr', 700), 0) }}',
        period: 'One-Time Payment',
        description: 'Our flagship plan. Elevates everything with premium custom templates and reports.',
        badge: 'Most Popular',
        isPopular: true,
        features: [
          { text: 'Up to 3 Events', highlight: true },
          { text: 'Premium Invitation Templates', highlight: true },
          { text: 'Premium Video / Reels Templates', highlight: false },
          { text: 'Priority Email & Chat Support', highlight: false },
          { text: 'Full Data Export & Email Reports', highlight: true }
        ],
        limits: {
          events: 3,
          entries: 999999,
          qrCollection: true,
          editors: 3
        }
      },
      {
        id: 'professional',
        tier: 6,
        name: 'Professional',
        price: {{ config('packs.professional.amount_inr', 999) }}, formattedPrice: '₹{{ number_format(config('packs.professional.amount_inr', 999), 0) }}',
        period: 'One-Time Payment',
        description: 'For power users and professional coordinators running multiple large events.',
        badge: 'Professional',
        isPopular: false,
        features: [
          { text: 'Up to 10 Events', highlight: true },
          { text: 'Unlimited Family Editors', highlight: true },
          { text: 'Advanced Analytics Dashboard', highlight: false },
          { text: 'Custom Branding (Remove logo)', highlight: true },
          { text: 'Event Backup & Restore Utilities', highlight: false },
          { text: 'Premium Support Channel', highlight: false }
        ],
        limits: {
          events: 10,
          entries: 999999,
          qrCollection: true,
          editors: 999
        }
      },
      {
        id: 'enterprise',
        tier: 7,
        name: 'Enterprise',
        price: 9999, // Threshold for custom pricing calculations
        formattedPrice: 'Custom',
        period: 'Contact Sales',
        description: 'Bespoke integration, white labeling, and dedicated hosting for large organizations.',
        badge: 'Enterprise',
        isPopular: false,
        features: [
          { text: 'Unlimited Events & Editors', highlight: true },
          { text: 'Organization Dashboard & Team Management', highlight: false },
          { text: 'White Label & Custom Domain Solution', highlight: true },
          { text: 'Full REST API Integrations', highlight: false },
          { text: 'Dedicated Account Manager', highlight: false }
        ],
        limits: {
          events: 9999,
          entries: 999999,
          qrCollection: true,
          editors: 999
        }
      }
    ];

    const FEATURE_MATRIX = {
      categories: [
        {
          name: 'Core Usage',
          features: [
            { key: 'events', name: 'Active Events Included', type: 'text' },
            { key: 'entries', name: 'Ledger Entries Per Event', type: 'text' },
            { key: 'ledger', name: 'Basic Ledger Management', type: 'bool' },
            { key: 'pdf_export', name: 'Standard PDF Export', type: 'bool' },
            { key: 'cash_cover', name: 'Cash & Cover Tracking', type: 'bool' }
          ]
        },
        {
          name: 'Invitations & Creativity',
          features: [
            { key: 'invitations', name: 'Invitation Templates', type: 'text' },
            { key: 'printable', name: 'Printable Invitation Designs', type: 'bool' },
            { key: 'video', name: 'Story / Reel Video Maker', type: 'text' },
            { key: 'countdown', name: 'Event Countdown Studio', type: 'bool' },
            { key: 'graphics', name: 'Social Post Graphic Studio', type: 'bool' }
          ]
        },
        {
          name: 'Payments & Collections',
          features: [
            { key: 'upi_qr', name: 'Personal UPI QR Code Collection', type: 'bool' },
            { key: 'payment_tracking', name: 'Guest Payment Tracking & Log', type: 'bool' },
            { key: 'reports', name: 'Financial Status Reports', type: 'text' },
            { key: 'full_pdf', name: 'Detailed Full Event PDF Export', type: 'bool' }
          ]
        },
        {
          name: 'Collaboration & Team',
          features: [
            { key: 'viewers', name: 'Family Viewers (Read-Only)', type: 'text' },
            { key: 'editors', name: 'Family Editors (Write Access)', type: 'text' },
            { key: 'shared_mgmt', name: 'Shared Event Workspace', type: 'bool' },
            { key: 'joint_hosting', name: 'Joint Family Hosting Support', type: 'bool' },
            { key: 'roles', name: 'Role-Based Permissions', type: 'bool' }
          ]
        },
        {
          name: 'Advanced & Support',
          features: [
            { key: 'support', name: 'Support Level', type: 'text' },
            { key: 'branding', name: 'Remove Brand Logos / Custom Branding', type: 'bool' },
            { key: 'backup', name: 'Event Data Backup & Restore', type: 'bool' },
            { key: 'dashboard', name: 'Organization Admin Dashboard', type: 'bool' }
          ]
        }
      ],
      values: {
        starter: {
          events: '1 Event', entries: 'Up to 50', ledger: true, pdf_export: true, cash_cover: true,
          invitations: 'Basic Text Only', printable: false, video: 'No', countdown: false, graphics: false,
          upi_qr: false, payment_tracking: false, reports: 'No', full_pdf: false,
          viewers: 'Up to 3', editors: 'None', shared_mgmt: false, joint_hosting: false, roles: false,
          support: 'Community Support', branding: false, backup: false, dashboard: false
        },
        celebration: {
          events: '1 Event', entries: 'Up to 50', ledger: true, pdf_export: true, cash_cover: true,
          invitations: '10 Standard Templates', printable: true, video: '1 Reels Video', countdown: true, graphics: true,
          upi_qr: false, payment_tracking: false, reports: 'No', full_pdf: false,
          viewers: 'Up to 3', editors: 'None', shared_mgmt: false, joint_hosting: false, roles: false,
          support: 'Community Support', branding: false, backup: false, dashboard: false
        },
        guest_contribution: {
          events: '1 Event', entries: 'Unlimited', ledger: true, pdf_export: true, cash_cover: true,
          invitations: '10 Standard Templates', printable: true, video: '1 Reels Video', countdown: true, graphics: true,
          upi_qr: true, payment_tracking: true, reports: 'Basic Reports', full_pdf: true,
          viewers: 'Up to 3', editors: 'None', shared_mgmt: false, joint_hosting: false, roles: false,
          support: 'Standard Email', branding: false, backup: false, dashboard: false
        },
        host_plus: {
          events: '2 Events', entries: 'Unlimited', ledger: true, pdf_export: true, cash_cover: true,
          invitations: '10 Standard Templates', printable: true, video: '1 Reels Video', countdown: true, graphics: true,
          upi_qr: true, payment_tracking: true, reports: 'Advanced PDF Reports', full_pdf: true,
          viewers: 'Up to 3', editors: 'None', shared_mgmt: false, joint_hosting: false, roles: false,
          support: 'Standard Email', branding: false, backup: false, dashboard: false
        },
        family: {
          events: '2 Events', entries: 'Unlimited', ledger: true, pdf_export: true, cash_cover: true,
          invitations: '10 Standard Templates', printable: true, video: '1 Reels Video', countdown: true, graphics: true,
          upi_qr: true, payment_tracking: true, reports: 'Advanced PDF Reports', full_pdf: true,
          viewers: 'Up to 3', editors: '3 Active Editors', shared_mgmt: true, joint_hosting: true, roles: true,
          support: 'Standard Email', branding: false, backup: false, dashboard: false
        },
        premium_host: {
          events: '3 Events', entries: 'Unlimited', ledger: true, pdf_export: true, cash_cover: true,
          invitations: 'All Premium Templates', printable: true, video: 'Premium Custom Videos', countdown: true, graphics: true,
          upi_qr: true, payment_tracking: true, reports: 'Email + PDF Export', full_pdf: true,
          viewers: 'Up to 3', editors: '3 Active Editors', shared_mgmt: true, joint_hosting: true, roles: true,
          support: 'Priority Support', branding: false, backup: false, dashboard: false
        },
        professional: {
          events: '10 Events', entries: 'Unlimited', ledger: true, pdf_export: true, cash_cover: true,
          invitations: 'All Premium Templates', printable: true, video: 'Premium Custom Videos', countdown: true, graphics: true,
          upi_qr: true, payment_tracking: true, reports: 'Advanced Analytics Dashboard', full_pdf: true,
          viewers: 'Unlimited', editors: 'Unlimited Editors', shared_mgmt: true, joint_hosting: true, roles: true,
          support: '24/7 Priority Support', branding: true, backup: true, dashboard: false
        },
        enterprise: {
          events: 'Unlimited', entries: 'Unlimited', ledger: true, pdf_export: true, cash_cover: true,
          invitations: 'All Premium Templates', printable: true, video: 'Premium Custom Videos', countdown: true, graphics: true,
          upi_qr: true, payment_tracking: true, reports: 'Advanced Analytics Dashboard', full_pdf: true,
          viewers: 'Unlimited', editors: 'Unlimited Editors', shared_mgmt: true, joint_hosting: true, roles: true,
          support: 'Dedicated Manager', branding: true, backup: true, dashboard: true
        }
      }
    };

    // Dark/Light Theme Switching
    

    // Render pricing cards
    const plansGrid = document.getElementById('plansGrid');

    function renderPricingCards() {
      plansGrid.innerHTML = '';
      PLANS.forEach((plan, index) => {
        const card = document.createElement('div');
        card.className = `card ${plan.isPopular ? 'highlighted' : ''}`;
        card.id = `card-${plan.id}`;

        // Build features checklist
        let featuresHtml = '';
        plan.features.forEach(f => {
          featuresHtml += `
            <li class="card-feature-item ${f.highlight ? 'highlight' : ''}">
              <div class="card-feature-icon">
                <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor"></path></svg>
              </div>
              <span>${f.text}</span>
            </li>
          `;
        });

        // Interconnection visual helper (if not the free starter plan)
        let inclusionHtml = '';
        if (index > 0) {
          const prevPlan = PLANS[index - 1];
          inclusionHtml = `
            <div class="card-inclusion-info">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
              <span>Everything in <strong>${prevPlan.name}</strong>, plus:</span>
            </div>
          `;
        } else {
          inclusionHtml = `
            <div class="card-inclusion-info" style="background: var(--bg); color: var(--text-muted);">
              <span>Core Event Ledger Essentials:</span>
            </div>
          `;
        }

        const popularBadge = plan.isPopular ? `<div class="card-badge">Most Popular</div>` : 
                             (plan.badge ? `<div class="card-badge" style="background: var(--accent);">${plan.badge}</div>` : '');

        card.innerHTML = `
          ${popularBadge}
          <div class="card-header">
            <h3 class="card-name">${plan.name}</h3>
            <div class="card-price-container">
              <span class="card-price">${plan.formattedPrice}</span>
              <span class="card-period">/ ${plan.period}</span>
            </div>
            <p class="card-description">${plan.description}</p>
          </div>
          
          ${inclusionHtml}

          <ul class="card-features-list">
            ${featuresHtml}
          </ul>

          ${(
              (plan.id === 'guest_contribution' && guestPayCredits > 0)
              || (plan.id === 'starter' && {{ Auth::check() ? 'true' : 'false' }})
              || (plan.tier !== 0 && plan.id !== 'guest_contribution' && plan.tier <= currentUserPlanLevel)
            )
            ? `<button class="card-action-btn" disabled style="background: var(--bg); color: var(--text-muted); cursor: not-allowed; border: 1px solid var(--border);">
                 <span>${plan.id === 'guest_contribution' ? 'Active (' + guestPayCredits + ' credit' + (guestPayCredits > 1 ? 's' : '') + ')' : 'Active Plan'}</span>
                 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                   <path d="M20 6L9 17l-5-5"></path>
                 </svg>
               </button>`
            : `<button class="card-action-btn" onclick="selectPlan('${plan.id}')">
                 <span>${plan.price === 9999 ? 'Contact Sales' : (plan.tier === 0 ? 'Start Free' : 'Get Started')}</span>
                 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                   <path d="M5 12h14M12 5l7 7-7 7"></path>
                 </svg>
               </button>`
          }
        `;
        plansGrid.appendChild(card);
      });
    }

    // Render Upgrade Hierarchy Steps
    const hierarchyFlow = document.getElementById('hierarchyFlow');
    const hierarchyProgress = document.getElementById('hierarchyProgress');

    function renderHierarchyFlow() {
      // Clear all steps but preserve progress bar
      const steps = hierarchyFlow.querySelectorAll('.hierarchy-step');
      steps.forEach(s => s.remove());

      PLANS.forEach((plan, index) => {
        const step = document.createElement('div');
        step.className = `hierarchy-step`;
        step.id = `step-${plan.id}`;
        step.addEventListener('click', () => highlightPlanHierarchy(plan.id));

        // Format step name to keep simple
        let label = plan.name.replace(' Plan', '');

        step.innerHTML = `
          <div class="hierarchy-node">${index + 1}</div>
          <div class="hierarchy-label">${label}</div>
        `;
        hierarchyFlow.appendChild(step);
      });
    }

    function highlightPlanHierarchy(planId, preventScroll = false) {
      const selectedIndex = PLANS.findIndex(p => p.id === planId);
      
      // Update steps visual class
      PLANS.forEach((plan, index) => {
        const step = document.getElementById(`step-${plan.id}`);
        const card = document.getElementById(`card-${plan.id}`);
        
        if (step) {
          step.classList.remove('active', 'active-path');
          if (index < selectedIndex) {
            step.classList.add('active-path');
          } else if (index === selectedIndex) {
            step.classList.add('active');
          }
        }

        // Highlight corresponding pricing card below
        if (card) {
          if (index === selectedIndex) {
            card.classList.add('highlighted');
            if (!preventScroll) {
              card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
          } else {
            if (plan.id !== 'premium_host') {
              card.classList.remove('highlighted');
            }
          }
        }
      });

      // Calculate progress width
      const progressPercent = selectedIndex === 0 ? 0 : (selectedIndex / (PLANS.length - 1)) * 100;
      hierarchyProgress.style.width = `calc(${progressPercent}% - ${selectedIndex === 0 ? '0px' : '40px'})`;
    }

    // Interactive Recommender logic
    const recommenderEvents = document.getElementById('recommenderEvents');
    const recommenderEntries = document.getElementById('recommenderEntries');
    const recommenderQR = document.getElementById('recommenderQR');
    const recommenderEditors = document.getElementById('recommenderEditors');

    const eventsVal = document.getElementById('eventsVal');
    const entriesVal = document.getElementById('entriesVal');
    const recommendationResult = document.getElementById('recommendationResult');

    function calculateRecommendation(e) {
      const preventScroll = (e === true);
      const events = parseInt(recommenderEvents.value);
      const entries = parseInt(recommenderEntries.value);
      const needQR = recommenderQR.checked;
      const needEditors = recommenderEditors.checked;

      // Update Labels
      eventsVal.innerText = events >= 15 ? '15+ Events' : `${events} ${events === 1 ? 'Event' : 'Events'}`;
      entriesVal.innerText = entries >= 1000 ? '1000+ Entries' : `${entries} Entries`;

      let recommendedPlan = PLANS[0]; // Starter default

      for (let i = 0; i < PLANS.length; i++) {
        const p = PLANS[i];
        let fits = true;

        // Check events constraint
        if (events > p.limits.events) fits = false;
        // Check entries constraint (starter & celebration max entries is 50)
        if (p.id === 'starter' || p.id === 'celebration') {
          if (entries > 50) fits = false;
        }
        // Check QR code collection requirements
        if (needQR && !p.limits.qrCollection) fits = false;
        // Check editors requirement
        if (needEditors && p.limits.editors === 0) fits = false;

        if (fits) {
          recommendedPlan = p;
          break;
        }
      }

      // If requirements exceed professional plan (like > 10 events or custom branding requirements), recommend Enterprise
      if (events > 10) {
        recommendedPlan = PLANS[PLANS.length - 1]; // Enterprise
      }

      // Render Recommender output card
      recommendationResult.innerHTML = `
        <div class="result-badge">Best Match for you</div>
        <div class="result-name">${recommendedPlan.name}</div>
        <div class="result-price">${recommendedPlan.formattedPrice}</div>
        <p class="result-desc">${recommendedPlan.description}</p>
        <button class="result-action-btn" onclick="selectPlan('${recommendedPlan.id}')">Choose Plan</button>
      `;

      // Visual trigger indicator
      recommendationResult.classList.remove('highlight-pulse');
      void recommendationResult.offsetWidth; // Trigger reflow
      recommendationResult.classList.add('highlight-pulse');

      // Highlight step and card
      highlightPlanHierarchy(recommendedPlan.id, preventScroll);
    }

    // Attach recommender listeners
    recommenderEvents.addEventListener('input', calculateRecommendation);
    recommenderEntries.addEventListener('input', calculateRecommendation);
    recommenderQR.addEventListener('change', calculateRecommendation);
    recommenderEditors.addEventListener('change', calculateRecommendation);


    // Comparison Matrix Rendering
    const comparisonTable = document.getElementById('comparisonTable');
    const tableFilterToggle = document.getElementById('tableFilterToggle');

    function renderComparisonTable(filter = 'all') {
      comparisonTable.innerHTML = '';
      
      // Build Table Header
      let headerHtml = '<tr><th>Feature Details</th>';
      PLANS.forEach(p => {
        if (filter === 'popular' && p.id !== 'starter' && p.id !== 'guest_contribution' && p.id !== 'premium_host' && p.id !== 'professional') {
          return; // Skip non-popular focus plans for comparison filter
        }
        headerHtml += `
          <th class="col-plan ${p.isPopular ? 'highlighted' : ''}">
            <div style="font-weight: 800;">${p.name}</div>
            <div style="font-size: 0.8rem; color: var(--primary); margin-top: 4px;">${p.formattedPrice}</div>
          </th>
        `;
      });
      headerHtml += '</tr>';
      comparisonTable.innerHTML += headerHtml;

      // Build rows category by category
      FEATURE_MATRIX.categories.forEach(cat => {
        // Category title row
        let catColspan = filter === 'popular' ? 5 : PLANS.length + 1;
        comparisonTable.innerHTML += `
          <tr class="category-row">
            <td colspan="${catColspan}">${cat.name}</td>
          </tr>
        `;

        // Render features under this category
        cat.features.forEach(feat => {
          let rowHtml = `<tr><td>${feat.name}</td>`;
          PLANS.forEach(p => {
            if (filter === 'popular' && p.id !== 'starter' && p.id !== 'guest_contribution' && p.id !== 'premium_host' && p.id !== 'professional') {
              return;
            }
            
            const cellVal = FEATURE_MATRIX.values[p.id][feat.key];
            
            if (feat.type === 'bool') {
              if (cellVal === true) {
                rowHtml += `
                  <td class="col-val">
                    <span class="check-icon">
                      <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor"></path></svg>
                    </span>
                  </td>
                `;
              } else {
                rowHtml += `
                  <td class="col-val">
                    <span class="cross-icon">—</span>
                  </td>
                `;
              }
            } else {
              rowHtml += `
                <td class="col-val">
                  <span class="table-val-text">${cellVal}</span>
                </td>
              `;
            }
          });
          rowHtml += '</tr>';
          comparisonTable.innerHTML += rowHtml;
        });
      });
    }

    // Comparison Filter toggle handlers
    tableFilterToggle.addEventListener('click', (e) => {
      if (e.target.classList.contains('comparison-toggle-btn')) {
        const btn = e.target;
        tableFilterToggle.querySelectorAll('.comparison-toggle-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderComparisonTable(btn.dataset.filter);
      }
    });

    // Alert purchase plan
    
    function selectPlan(planId) {
      const planRoutes = {
        'starter': '{{ route("client.dashboard") }}',
        'celebration': '{{ route("client.packs.checkout", "celebration") }}',
        'guest_contribution': '{{ route("client.packs.checkout", "guest-pay-single") }}',
        'host_plus': '{{ route("client.packs.checkout", "host-duo") }}',
        'family': '{{ route("client.packs.checkout", "family") }}',
        'premium_host': '{{ route("client.packs.checkout", "bundle") }}',
        'professional': '{{ route("client.packs.checkout", "professional") }}',
        'enterprise': '{{ route("client.packs.checkout", "enterprise") }}'
      };
      
      const route = planRoutes[planId];
      if (route) {
        window.location.href = route;
      }
    }

    // Initialize all components on load
    window.addEventListener('DOMContentLoaded', () => {
      renderPricingCards();
      renderHierarchyFlow();
      calculateRecommendation(true);
      renderComparisonTable();
    });
  
</script>
