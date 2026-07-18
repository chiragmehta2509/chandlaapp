
    // Data model representing the interconnected pricing structure
    const PLANS = [
      {
        id: 'starter',
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
        name: 'Celebration Plan',
        price: 300,
        formattedPrice: '₹300',
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
        name: 'Guest Contribution',
        price: 400,
        formattedPrice: '₹400',
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
        name: 'Host Plus Plan',
        price: 500,
        formattedPrice: '₹500',
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
        name: 'Family Plan',
        price: 600,
        formattedPrice: '₹600',
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
        name: 'Premium Host Plan',
        price: 700,
        formattedPrice: '₹700',
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
        name: 'Professional Plan',
        price: 999,
        formattedPrice: '₹999',
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
        name: 'Enterprise Plan',
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
    const themeToggleBtn = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    function initTheme() {
      const storedTheme = localStorage.getItem('theme') || 'light';
      document.documentElement.setAttribute('data-theme', storedTheme);
      updateThemeIcon(storedTheme);
    }

    function toggleTheme() {
      const currentTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateThemeIcon(newTheme);
    }

    function updateThemeIcon(theme) {
      if (theme === 'dark') {
        themeIcon.innerHTML = `<path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>`;
      } else {
        themeIcon.innerHTML = `<path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707.707M12 7a5 5 0 100 10 5 5 0 000-10z"></path>`;
      }
    }

    themeToggleBtn.addEventListener('click', toggleTheme);
    initTheme();

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

          <button class="card-action-btn" onclick="selectPlan('${plan.id}')">
            <span>${plan.price === 9999 ? 'Contact Sales' : 'Get Started'}</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"></path>
            </svg>
          </button>
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

    function highlightPlanHierarchy(planId) {
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
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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

    function calculateRecommendation() {
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
      highlightPlanHierarchy(recommendedPlan.id);
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
      const plan = PLANS.find(p => p.id === planId);
      alert(`Thank you for selecting the ${plan.name} (${plan.formattedPrice})! In a production app, this will redirect to secure payment gateways and register features on your user profile.`);
    }

    // Initialize all components on load
    window.addEventListener('DOMContentLoaded', () => {
      renderPricingCards();
      renderHierarchyFlow();
      calculateRecommendation();
      renderComparisonTable();
    });
  