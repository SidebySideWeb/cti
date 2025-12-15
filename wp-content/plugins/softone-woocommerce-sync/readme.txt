=== SoftOne ↔ WooCommerce Sync ===
Contributors: sidebysideweb
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 0.1.0
License: GPLv2 or later

== Description ==
Bi-directional integration with SoftOne ERP:
- ERP → Woo: Products, Customers
- Woo → ERP: Orders as SALDOC with ITELINES

== Setup ==
1. Install & Activate.
2. WooCommerce → SoftOne Settings: add endpoint, credentials, appId, company, branch, refid, userid.
3. Tools → SoftOne Sync to run manual syncs.
4. Cron runs hourly (products) / twice daily (customers).

== Notes ==
- Map MTRL properly. If your ERP uses CODE instead of numeric MTRL in SetData, adjust Order_Sync accordingly.
- Adjust browser list names to your SoftOne setup.
