==================================
 FRAGMENT FAKE OFFER — INSTRUCTIONS
==================================

📁 LOCATION
The bot is located in the folder:
    /telegram-bot/

-------------------------------

🛠 INSTALLING BOT DEPENDENCIES
Open a terminal in the bot's folder and run the command:
    pip install -r requirements.txt

-------------------------------

🛠 INSTALLING THE WEBSITE ON THE SERVER
Be sure to install:
    - php
    - php-fpm
    - php-json
    
    * grant 777 permissions to the file /telegram-bot/offers.json

-------------------------------

🤖 CREATING AND CONFIGURING THE BOT
Go to Telegram and open @BotFather
    1. Enter the command: /newbot — follow the instructions
    2. After creation, go to: /mybots → Bot Settings → Inline Mode → Turn on
    3. Run the command: /setinline → select the bot and follow the instructions

-------------------------------

⚙ CONFIGURING .env
In the .env file, specify:
    BOT_TOKEN=your_bot_token
    OFFER_URL=https://your-domain.com
    TON_COMSSION=deal_commission
    MESSAGE_TEMPLATE=message_variant

-------------------------------

✅ USING THE BOT
In any chat, type:
    @your_bot phone_number/username price

Examples:
    @my_bot +1234567890 400
    @my_bot @nickname $300

-------------------------------

📌 FORMAT RULES:

• Phone number:
      With or without "+": +1234567890 or 1234567890

• Username:
      With or without "@": @nickname or nickname

• Price:
    - Just a number (e.g., 400) → specified in TON
    - With a dollar sign (e.g., $400 or 400$) → automatically converted to TON

    Example:
    @mybot @boredmonkeyman 400 = 400 TON (~1320$)
    @mybot @boredmonkeyman 400$ = 121.21 TON (~400$)

-------------------------------

Done! The bot is working 🎉
