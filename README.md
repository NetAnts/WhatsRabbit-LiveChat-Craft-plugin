# What'sRabbit Live-chat

This plugin injects a Live-chat widget into your website (when necessary config is set) via a Twig hook used in your page template.

### Installing the plugin
1. Require the plugin via composer `composer require netants/whatsrabbit-live-chat`
2. Install the plugin in your CraftCMS dashboard
   1. Login in your CraftCMS dashbord
   2. Click "Settings" in the left sidebar
   3. Click "Plugins"
   4. Click on the cog icon on the right side and click "Install"
3. Within the sidebar on the left, you'll see an envelope icon with "What'sRabbit Live-chat" next to it.
   1. Click on it
4. Now, you'll see a number of fields that require value.
   1. `API Key` _The api key provided by What'sRabbit_
   2. `API Secret` _The api secret provided by What'sRabbit_
   3. `Avatar` _An avatar which will show in the Live-chat widget on your website_
   4. `Title` _The title which will show in the Live-chat widget on your website_
   5. `Description` _The description/short text which will show in the Live-chat widget on your website_
   6. `WhatsApp URL` _The WhatsApp button in the Live-chat widget will redirect to this URL when a user does not want to start a Live-chat_
5. Now it's time to add the following Twig hook to your page template: `{% hook 'whatsrabbit-live-chat' %}`. 
6. Once all of these steps have been done, the Live-chat widget will show on your website and you can start with receiving Live-chats within your What'sRabbit application. 
