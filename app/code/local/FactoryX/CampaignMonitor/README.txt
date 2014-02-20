Changelogs

== 0.7.2
- Support for a "don't send me a welcome email" feature

== 0.7.1
- Sync info between Mage & CM
- Fix the issue where new account creation with subscribe option doesn't get the subscriber promotion

* Test Procedure (e.g. with email alvin@factoryx.com.au)

-- Clean out information of the email both in Mage and CM
-- Subscribe using front end to confirm form subscription
-- Confirm information pushed to both Mage and CM with promotion

-- Clean out information in Mage
-- Try to subscribe using from subscription (will get denied due to already subscribed)
-- Confirm information synced from CM to Mage

-- Clean out information in CM
-- Try to subscribe using from subscription (will get denied due to already subscribed)
-- Confirm information synced from Mage to CM

-- Clean out information of the email both in Mage and CM
-- Try to place an order as guest with subscription
-- Confirm new subscription with promotion

-- Clean out information of the email both in Mage and CM
-- Try to place an order as new customer with subscription
-- Confirm new subscription with promotion