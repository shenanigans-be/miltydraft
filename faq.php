<div class="tab" id="faq">
    <div class="question content-wrap">
        <h3>FAQ</h3>

        <h4>What am I supposed to do when it's my turn?</h4>
        <p>
            You should pick either a faction, a slice or a position relative to speaker.
        </p>

        <h4>What is a milty draft?</h4>

        <p>
            <em>(from miltydraft.com)</em>
        </p>

        <p>
            Milty Draft is a method for setting up a game of <i>Twilight Imperium: Fourth Edition<sup>®</sup></i> with the <i>Prophecy of Kings<sup>®</sup></i> expansion, by Fantasy Flight Games<sup>™</sup> (not affiliated with this website).
            It allows players to build a map, choose their faction, and organise a seating order in a balanced manner.
        </p>
        <p>
            The draft takes place over three rounds. Every round, all players get to pick their a) faction or b) map slice or c) table position (including the speaker position) until they have one of each.
            The order that players make these choices is random in the first round. The second round picks are done in the reverse order, and the third round reverts to the same order as the first round, making a boustrophedon draft.
            Once all players have made their choice, the map is assembled from the slices, and players can start the game proper.
        </p>
        <p>
            Each slice consists of five systems, excluding a player’s home system. These comprise of the four systems closer to that player’s home system than any other player’s
            (two in the outer ring, one in the middle ring, and one in the inner ring), as well as the system in ring two that is equidistant to that player’s home system and the home system of the player on their left. The slices are roughly balanced using a tiered system of tiles. Each blue–backed tile is assigned one of three tiers. Then, each slice is randomly assigned one tile from each tier, along with two red–back tiles. Each slice must meet a minimum influence and resource threshold,
            as well as a minimum and maximum total threshold. In addition, slices cannot contain two of the same type of wormhole, or anomalies next to one another.
            There is an option to force the draft to include a minimum number of legendary planets, and wormholes of each type. Should these conditions not be met,
            then another set of slices is generated. Because of the way slices are assembled, anomalies may be placed next to one another if they were on the borders of
            two slices.
        </p>

        <h4>What does "optimal value" mean?</h4>
        <p>
            The “Optimal Value” of a planet is calculated by using the higher of its resource value and influence value as that value, and the other value as zero.
            If both of the planet’s resource value and influence value are equal, half that value is used for both of its optimal values.
            For example, Starpoint, a 3/1, is treated as 3/0, Corneeq, a 1/2, is treated as 0/2, and Rigel III, a 1/1, is treated as ½/½.
        </p>
        <p>
            The optimal value of a system, then, is just the total of all the planet's optimal values.
        </p>

        <h4>How long can my draft stay online?</h4>
        <p>
            Well, that kind of depends on how popular this gets. Hosting isn't free, sadly, so I'm planning to weed out drafts older than one year every so often.
        </p>

        <h4>Can I help?</h4>
        <p>
            Sure you can! This is an open source project, if you're a programmer you can check out if there's any <a href="https://github.com/shenanigans-be/miltydraft/issues" target="_blank">open issues</a> you might want to tacke.<br />
            If you're not technically inclined: you can make a paypal donation to help pay for hosting-costs. <br />
            Please don't feel like you have to. I'm more than happy to keep this site running by myself as my gift to a wonderful community.
        </p>
        <form action="https://www.paypal.com/donate" method="post" target="_top">
            <input type="hidden" name="business" value="5JW6PWQTX8C58" />
            <input type="hidden" name="no_recurring" value="0" />
            <input type="hidden" name="item_name" value="Hi there! Thank you so much for considering a donation to the Miltydraft tool." />
            <input type="hidden" name="currency_code" value="EUR" />
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
            <img alt="" border="0" src="https://www.paypal.com/en_BE/i/scr/pixel.gif" width="1" height="1" />
        </form>


        <h4>Why only five players without PoK?</h4>
        <p>
            Technically it could go up to six (6 is a hard limit because there are only 12 red tiles in the base game), but here's why only five:<br />
            The way slices are generated now is that each slice gets a top-, middle- and lower-tier tile + 2 red ones. And in the tiers I have (from miltydraft.com, who — I assume — did their homework) only have 5 middle-tier tiles listed for the base game.<br />
            SO: if I completely leave out the tier-system for non-PoK games, it could do six. and I'm planning to implement that in *some* way,<br />
            but it involves some deeper-level tinkering and also figuring out how to communicate this whole mess above clearly to anyone using these settings (without cluttering up the whole thing with even more help-text than it already has).<br />
            I might implement all this and you'll be able to go up to six, I'm working my way through feedback and feature requests, but I also have a day job.
        </p>

        <h4>I picked Council Keleres and my homesystem doesn't show up on the map!</h4>
        <p>
            It's difficult to integrate Keleres into the draft gracefully. There are a lot of differing opinions on how/when they should pick their
            flavor and it just seemed like the best option to leave it up to each group to decide amongst themselves. So because of that — in short — no: The Council's homesystem doesn't show up because the draft system doesn't know what the correct system is.
        </p>


        <h4>What's your privacy policy?</h4>
        <p>
            As you can see, links are public to anyone who has them, so there's technically no guarantee to privacy. I'd say: be mindful of any information you enter as player names.<br />
            Other than that we collect no personal data from you.
        </p>

        <h4>How does claiming work?</h4>
        <p>
            Once you claim a player your browser will remember you for this draft, so you don't have to keep this tab open the whole time. <br />
            If you lose access to whatever browser/device you claimed your place on the admin can make picks for you.<br />
            Pretty much the same thing happens for admins though, so if both the admin and you lose access at the same time then you're pretty much boned.
        </p>

        <h4>This thing is awesome!</h4>
        <p>
            Not really a question, but I appreciate the enthousiasm!<br />
            This website is based on (read: plagiarised entirely from) miltydraft.com, but extended to have a saved shareable links and saving draft over sessions/multiple users.<br />
            Also thanks to <a href="https://github.com/KeeganW/ti4">KeeganW</a> for the nicely structured data and tile images, and to Farthom for the additional security consultancy.<br /><br />
            I am not affiliated with Fantasy Flight or Twilight Imperium. Sorry if I'm doing something that's not allowed, copyright-wise.
        </p>

        <h4>This is thing is hot garbage! / It's missing feature X which I desperately need!</h4>
        <p>
            First of all: rude.<br />
            Second: Yes, there are a lot of problems with this, and stuff I haven't implemented, sure.<br />
            The code is on <a href="https://github.com/shenanigans-be/miltydraft" target="_blank">Github</a> if you want to report an issue or even contribute!<br />
            If you'd like to reach out to me directly, you can do so on <a href="https://twitter.com/samtubbax" target="_blank">Twitter</a> or <a href="https://www.reddit.com/user/notcleverenough" target="_blank">Reddit</a>.
        </p>

        <h4>Oh no, I did something wrong! How do I fix it?</h4>
        <p>
            You can ask the admin to Undo the last pick. The undo button can be found in the admin's "Log" tab.
        </p>

        <h4><strong style="opacity: 0.6">June 2023</strong>: What's all this new stuff?</h4>
        <p>
            The tool now supports the Discordant Stars expansion (big shoutout to <a href="https://github.com/JoelNarr" target="_blank">JoelNarr</a> who did most of the work)<br />
            and I've added functionality for admins to regenerate the options if they're not entirely to their liking. As well as a "slice"-view in the map tab.
        </p>

    </div>
</div>