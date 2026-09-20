# Graph Report - appweb  (2026-09-20)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 568 nodes · 1025 edges · 90 communities (14 shown, 76 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 35 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `1cf8720b`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Community 0
- Community 1
- Community 2
- Community 3
- Community 4
- Community 5
- Community 6
- Community 7
- Community 8
- Community 9
- Community 10
- Community 11
- Community 12
- Community 13
- Community 14
- Community 15
- Community 16
- Community 17
- Community 18
- Community 40
- Community 41
- Community 42
- Community 43
- Community 44

## God Nodes (most connected - your core abstractions)
1. `Order` - 44 edges
2. `User` - 40 edges
3. `Product` - 34 edges
4. `Category` - 33 edges
5. `Controller` - 30 edges
6. `Cart` - 22 edges
7. `Coupon` - 20 edges
8. `GHNService` - 20 edges
9. `OderItem` - 18 edges
10. `FullFlowTest` - 16 edges

## Surprising Connections (you probably didn't know these)
- `OrderController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Admin/OrderController.php → app/Http/Controllers/Controller.php
- `AdminController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/AdminController.php → app/Http/Controllers/Controller.php
- `PasswordResetController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Auth/PasswordResetController.php → app/Http/Controllers/Controller.php
- `OrderController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/OrderController.php → app/Http/Controllers/Controller.php
- `GHNOrderService` --references--> `GHNService`  [EXTRACTED]
  app/Services/GHNOrderService.php → app/Services/GHNService.php

## Import Cycles
- None detected.

## Communities (90 total, 76 thin omitted)

### Community 0 - "Community 0"
Cohesion: 0.06
Nodes (13): OrderController, AdminController, PasswordResetController, OrderController, Order, GHNOrderService, Carbon\Carbon, Illuminate\Foundation\Application (+5 more)

### Community 1 - "Community 1"
Cohesion: 0.06
Nodes (19): BrandController, CategoryController, CheckoutController, Controller, HomeController, ProductController, ProfilesController, Brand (+11 more)

### Community 2 - "Community 2"
Cohesion: 0.06
Nodes (16): OderItem, User, OderItemPolicy, Illuminate\Auth\Access\Response, Illuminate\Contracts\Auth\CanResetPassword, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase (+8 more)

### Community 3 - "Community 3"
Cohesion: 0.04
Nodes (45): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+37 more)

### Community 4 - "Community 4"
Cohesion: 0.08
Nodes (12): CartController, Cart, CartItem, CartService, OrderService, Illuminate\Http\Client\ConnectionException, Illuminate\Support\Collection, Illuminate\Support\Facades\Auth (+4 more)

### Community 5 - "Community 5"
Cohesion: 0.13
Nodes (7): CouponController, Coupon, HistorySearch, OrderItem, PaymentTransaction, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model

### Community 6 - "Community 6"
Cohesion: 0.11
Nodes (5): OderItemController, PlaceOrderRequest, StoreOderItemRequest, UpdateOderItemRequest, Illuminate\Foundation\Http\FormRequest

### Community 7 - "Community 7"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 9 - "Community 9"
Cohesion: 0.19
Nodes (5): DatabaseSeeder, OderItemSeeder, SampleDataSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 10 - "Community 10"
Cohesion: 0.27
Nodes (5): OderItemFactory, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, static

### Community 11 - "Community 11"
Cohesion: 0.33
Nodes (5): AdminMiddleware, Authenticate, Closure, Illuminate\Auth\Middleware\Authenticate, Symfony\Component\HttpFoundation\Response

### Community 12 - "Community 12"
Cohesion: 0.29
Nodes (5): AppServiceProvider, Illuminate\Pagination\Paginator, Illuminate\Support\Facades\Cache, Illuminate\Support\Facades\View, Illuminate\Support\ServiceProvider

### Community 16 - "Community 16"
Cohesion: 0.38
Nodes (3): PHPUnit\Framework\TestCase, ExampleTest, HelloWorldTest

### Community 17 - "Community 17"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

## Knowledge Gaps
- **51 isolated node(s):** `pestphp/pest-plugin`, `php-http/discovery`, `optimize-autoloader`, `preferred-install`, `sort-packages` (+46 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 234 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **76 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Category` connect `Community 1` to `Community 2`, `Community 4`, `Community 5`, `Community 9`, `Community 12`?**
  _High betweenness centrality (0.094) - this node is a cross-community bridge._
- **Why does `User` connect `Community 2` to `Community 0`, `Community 9`, `Community 5`, `Community 1`?**
  _High betweenness centrality (0.067) - this node is a cross-community bridge._
- **Why does `Order` connect `Community 0` to `Community 1`, `Community 2`, `Community 4`, `Community 5`, `Community 9`?**
  _High betweenness centrality (0.067) - this node is a cross-community bridge._
- **What connects `pestphp/pest-plugin`, `php-http/discovery`, `optimize-autoloader` to the rest of the system?**
  _51 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Community 0` be split into smaller, more focused modules?**
  _Cohesion score 0.05625 - nodes in this community are weakly interconnected._
- **Should `Community 1` be split into smaller, more focused modules?**
  _Cohesion score 0.0625 - nodes in this community are weakly interconnected._
- **Should `Community 2` be split into smaller, more focused modules?**
  _Cohesion score 0.06253652834599649 - nodes in this community are weakly interconnected._