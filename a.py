import requests
import json
import urllib.parse
import time

def get_event_urls(event_slug):
    url = f"https://partners.district.in/gw/consumer/events/v1/event/getEventContent/{event_slug}"
    headers = {
        "accept": "*/*",
        "user-agent": "Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1",
        "platform": "district_web",
        "x-guest-token": "1212"
    }

    response = requests.get(url, headers=headers)

    if response.status_code != 200:
        return json.dumps({"error": "Failed to fetch event data", "status_code": response.status_code})

    data = response.json().get("data", {})
    queue_id = data.get("queue_configuration", {}).get("queue_id", "")

    current_time = int(time.time() * 1000)  # Current time in UTC milliseconds

    condition_value = None
    pre_sale_end_time = 0

    for sale in data.get("sales", []):
        if sale.get("sale_type") == "pre_reg_sale":
            pre_sale_end_time = sale.get("end_utc_timestamp", 0)
            for condition in sale.get("sales_conditions", []):
                if condition.get("condition_type") == "source_equals_partner_site":
                    condition_value = condition.get("condition_value")
                    break

    general_target = f"https://www.district.in/event/{event_slug}/buy-page/shows/"
    encoded_general_target = urllib.parse.quote(general_target, safe='')

    partner_target = f"https://partners.district.in/events/{condition_value}-partner/{event_slug}" if condition_value else ""
    encoded_partner_target = urllib.parse.quote(partner_target, safe='')

    if current_time < pre_sale_end_time:
        # If current time is before pre-sale end time, use queue ID for pre-sales
        partner_queue_url = f"https://queue.partners.district.in/{queue_id}?target={encoded_partner_target}" if condition_value else ""
        general_queue_url = "General queue not generated before pre-sales ends"
    else:
        # After pre-sales ends, treat it as general queue
        partner_queue_url = ""
        general_queue_url = f"https://queue.district.in/{queue_id}?target={encoded_general_target}"

    general_direct_url = general_target
    partner_direct_url = partner_target if condition_value else ""

    return json.dumps({
        "general_queue_url": general_queue_url,
        "general_direct_url": general_direct_url,
        "partner_queue_url": partner_queue_url,
        "partner_direct_url": partner_direct_url
    }, indent=4)

# Example usage
print(get_event_urls("tata-ipl-2025-match-43-chennai-super-kings-vs-sunrisers-hyderabad-in-chennai-april25"))