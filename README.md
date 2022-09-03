# Tagger Coupons for Commerce

Allows creating coupons for multiple products at once by using [Tagger tags](https://github.com/modxcms/Tagger).

**Note**: The target field on your commerce_product records **MUST** point to its equivalent MODX resource record for this module to work. When you setup a coupon with this module, it will apply the discount to ALL possible product variations where the target resource is in a Tagger tag. You can exclude individual products if needed when setting up the coupon.

## Installation

1. Download the MODX install package
2. Install it in the MODX package manager, enable it in Commerce -> Modules
3. Setup new discounts under Commerce -> Discounts -> Tagger Coupons

**Important Note**: When you setup a Tagger Coupon in the Tagger Coupons menu, it will also display in the Coupons menu. **Do not edit it here**. Only edit it under Tagger Coupons. This is an issue with how Commerce core loads coupons and its derivative classes.