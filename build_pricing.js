const fs = require('fs');

let css = fs.readFileSync('extracted_style.css', 'utf8');
let html = fs.readFileSync('extracted_body.html', 'utf8');
let js = fs.readFileSync('extracted_script.js', 'utf8');

// 1. Process CSS
css = css.replace(/\[data-theme="dark"\]/g, 'html.dark .interconnected-pricing');

// 2. Process HTML
html = html.replace(/<div class="top-bar">[\s\S]*?<\/div>/, '');

// 3. Process JS
js = js.replace(/const themeToggleBtn = document\.getElementById\('themeToggle'\);[\s\S]*?initTheme\(\);/m, '');

// Replace fixed prices with blade config statements
js = js.replace(/price: 300,\s*formattedPrice: '₹300'/g, "price: {{ config('packs.celebration.amount_inr', 300) }}, formattedPrice: '₹{{ number_format(config('packs.celebration.amount_inr', 300), 0) }}'");
js = js.replace(/price: 400,\s*formattedPrice: '₹400'/g, "price: {{ config('packs.guest_pay_single.amount_inr', 400) }}, formattedPrice: '₹{{ number_format(config('packs.guest_pay_single.amount_inr', 400), 0) }}'");
js = js.replace(/price: 500,\s*formattedPrice: '₹500'/g, "price: {{ config('packs.ledger_duo.amount_inr', 500) }}, formattedPrice: '₹{{ number_format(config('packs.ledger_duo.amount_inr', 500), 0) }}'");
js = js.replace(/price: 600,\s*formattedPrice: '₹600'/g, "price: {{ config('packs.family.amount_inr', 600) }}, formattedPrice: '₹{{ number_format(config('packs.family.amount_inr', 600), 0) }}'");
js = js.replace(/price: 700,\s*formattedPrice: '₹700'/g, "price: {{ config('packs.premium_bundle.amount_inr', 700) }}, formattedPrice: '₹{{ number_format(config('packs.premium_bundle.amount_inr', 700), 0) }}'");
js = js.replace(/price: 999,\s*formattedPrice: '₹999'/g, "price: {{ config('packs.professional.amount_inr', 999) }}, formattedPrice: '₹{{ number_format(config('packs.professional.amount_inr', 999), 0) }}'");

const selectPlanReplacement = `
    function selectPlan(planId) {
      const planRoutes = {
        'starter': '{{ route("client.dashboard") }}',
        'celebration': '{{ route("client.packs.checkout", "celebration") }}',
        'guest_contribution': '{{ route("client.packs.checkout", "guest-pay-single") }}',
        'host_plus': '{{ route("client.packs.checkout", "host-duo") }}',
        'family': '{{ route("client.packs.checkout", "family") }}',
        'premium_host': '{{ route("client.packs.checkout", "bundle") }}',
        'professional': '{{ route("client.packs.checkout", "professional") }}',
        'enterprise': '{{ route("client.contact") }}'
      };
      
      const route = planRoutes[planId];
      if (route) {
        window.location.href = route;
      }
    }
`;
js = js.replace(/function selectPlan\s*\(\s*planId\s*\)\s*\{[\s\S]*?\}/m, selectPlanReplacement);

const finalBlade = `
<style>
${css}
</style>

<div class="interconnected-pricing">
  ${html}
</div>

<script>
${js}
</script>
`;

fs.writeFileSync('resources/views/partials/pricing-section.blade.php', finalBlade);
console.log('Successfully wrote pricing-section.blade.php');
