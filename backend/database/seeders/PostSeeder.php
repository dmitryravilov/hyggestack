<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Post Seeder
 *
 * Seeds posts with hygge-inspired content.
 */
class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $writer = User::where('email', 'emma@hyggestack.local')->first();
        $categories = Category::all();
        $tags = Tag::all();

        $posts = [
            [
                'title' => 'Why slower mornings matter',
                'excerpt' => 'The alarm goes off and the phone is already in hand. What if we started differently?',
                'content' => "# Why slower mornings matter\n\nThe alarm goes off and the phone is already in hand. Emails, news, messages. The day begins in a rush.\n\nI tried something different. No phone for the first hour. Just tea, a notebook, and morning light. The difference surprised me.\n\nIt's not about sleeping in. It's about creating space before demands arrive. For me, that means reading a few pages or writing in a journal.\n\nSome mornings I fail. The phone wins. But when I protect that first hour, everything feels different. The emails still come, but I'm better equipped.\n\nTry it for a week. Give yourself that first hour back.",
                'category_id' => $categories->where('slug', 'lifestyle')->first()->id,
                'tags' => ['cozy', 'mindfulness', 'slow-living', 'self-care'],
            ],
            [
                'title' => 'My grandmother\'s cinnamon rolls',
                'excerpt' => 'This recipe has been in my family for three generations. It requires time and attention.',
                'content' => "# My grandmother's cinnamon rolls\n\nThis recipe has been in my family for three generations. It's not complicated, but it requires time. That's what makes it special.\n\nMy grandmother learned it from her mother. Each generation made small adjustments, but the core remains: flour, yeast, plant-based butter, sugar, and patience.\n\n## Ingredients\n\n**For the dough:**\n- 4 cups all-purpose flour\n- 1/4 cup sugar\n- 1 tsp salt\n- 1 packet active dry yeast\n- 1 cup warm oat milk\n- 1/4 cup plant-based butter, melted\n- 1 tbsp ground flaxseed mixed with 3 tbsp water\n\n**For the filling:**\n- 1/2 cup plant-based butter, softened\n- 3/4 cup brown sugar\n- 2 tbsp cinnamon\n\n**For the glaze:**\n- 1 cup powdered sugar\n- 2 tbsp oat milk\n- 1/2 tsp vanilla\n\n## Instructions\n\nMix the yeast with warm oat milk and let it sit for five minutes until it foams. Combine the flour, sugar, and salt in a large bowl. Add the yeast mixture, melted plant-based butter, and flaxseed mixture. Mix until it forms a shaggy dough, then knead on a floured surface for about 8 minutes until smooth.\n\nPlace in a greased bowl, cover, and let rise in a warm place for about an hour, until doubled in size.\n\nRoll out the dough into a rectangle. Spread the softened plant-based butter over it, then sprinkle with the brown sugar and cinnamon mixture. Roll it up tightly, then cut into 12 pieces.\n\nPlace the rolls in a greased 9x13 inch pan, cover, and let rise again for 30 minutes. Bake at 375°F for 20-25 minutes, until golden brown.\n\nWhile still warm, drizzle with the glaze made from powdered sugar, oat milk, and vanilla.\n\nThese are best shared. Make them on a Sunday morning and invite someone over.",
                'category_id' => $categories->where('slug', 'recipes')->first()->id,
                'tags' => ['baking', 'coffee', 'comfort', 'warmth'],
            ],
            [
                'title' => 'The case for paying attention',
                'excerpt' => 'We talk about paying attention, but what does it actually mean? It might be simpler than we think.',
                'content' => "# The case for paying attention\n\nLast week I was walking to the shop, phone in hand, scrolling through messages. I didn't notice the cherry blossoms that had just bloomed. I didn't notice the afternoon light hitting the buildings.\n\nWhen I got home, I realized I couldn't remember the walk at all. It was just a blur between point A and point B.\n\nThis isn't about apps or expensive retreats. It's about noticing. When was the last time you really looked at something? Really listened? Really tasted your food?\n\nI started a small experiment. Once a day, I pick one thing and pay full attention to it. Yesterday it was my morning tea. I noticed the steam rising, the warmth of the cup, the way the light hit the surface. It took maybe two minutes, but it changed how I felt for the rest of the morning.\n\nTry it. Pick one thing today and really pay attention. See what happens.",
                'category_id' => $categories->where('slug', 'wellness')->first()->id,
                'tags' => ['mindfulness', 'peace', 'gratitude', 'simplicity'],
            ],
            [
                'title' => 'The small spaces that matter',
                'excerpt' => 'You don\'t need a whole room to create a place that feels like yours. Sometimes a corner is enough.',
                'content' => "# The small spaces that matter\n\nYou don't need a whole room to create a place that feels like yours. Sometimes a corner is enough.\n\nI live in a small flat. When I moved in, I kept looking for space I didn't have. A home office. A reading room. A place to write.\n\nThen I noticed the corner by the window. It was just enough space for a chair, a small table, and a lamp. I put a plant on the table, a blanket over the chair, and suddenly I had a reading nook.\n\nIt's become my favorite spot. In the morning, I sit there with tea and a book. In the evening, I write there. It's not fancy, just a corner, but it's mine.\n\nWhat makes a space feel good? For me, it's natural light, comfortable seating, and a few things that matter: books, plants, a good lamp. Nothing expensive, nothing complicated.\n\nLook around your home. Is there a corner you're not using? A window seat? A spot by a radiator? With a chair, a light, and maybe a plant, any small space can become something more.\n\nIt doesn't have to be perfect. It just has to be yours.",
                'category_id' => $categories->where('slug', 'home')->first()->id,
                'tags' => ['cozy', 'home', 'comfort', 'simplicity'],
            ],
            [
                'title' => 'Three things before bed',
                'excerpt' => 'A simple practice that changed how I end my days.',
                'content' => "# Three things before bed\n\nA simple practice that changed how I end my days.\n\nLast year, I started writing down three things I was grateful for before going to sleep. Not the big things, the small ones. The way my cat curled up on my lap. A perfectly brewed cup of tea. A conversation that made me laugh. The feeling of clean sheets.\n\nIt sounds simple, maybe even a bit trite. But it works.\n\nI keep a small notebook by my bed. Each night, I write three specific things. Some days it's easy. Other days I have to think harder. But I always find three things.\n\nThis isn't about denying difficulty or pretending everything is fine. It's about noticing what's also there, alongside the challenges. Life is complicated. It can be both hard and good at the same time.\n\nTry it for a week. Before you go to sleep, write down three specific things you're grateful for. See what happens.",
                'category_id' => $categories->where('slug', 'reflections')->first()->id,
                'tags' => ['gratitude', 'mindfulness', 'peace', 'reflections'],
            ],
        ];

        foreach ($posts as $postData) {
            $postTags = collect($postData['tags'])->map(function ($tagSlug) use ($tags) {
                return $tags->where('slug', $tagSlug)->first()->id ?? null;
            })->filter()->toArray();

            unset($postData['tags']);

            $post = Post::create([
                ...$postData,
                'author_id' => $writer->id,
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 30)),
            ]);

            $post->tags()->attach($postTags);
        }
    }
}
