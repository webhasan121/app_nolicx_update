import { Link } from "@inertiajs/react";
import DisplayCategory from "../components/client/DisplayCategory";
import ProductsLoop from "../components/client/ProductsLoop";
import Container from "../components/dashboard/Container";
import StickyNav from "../components/StickyNav";
import HeroSlider from "../components/home/HeroSlider";
import NewProduct from "../components/home/NewProduct";
import StaticSlider from "../components/home/StaticSlider";
import TodaysProducts from "../components/home/TodaysProducts";
import UserLayout from "../Layouts/User/App";
import RecommendedProducts from "../components/home/RecommendedProducts";
import TopSales from "../components/home/TopSales";

export default function Welcome({
    products = [],
    categories = [],
    ss = [],
    slides = [],
    recommended = [],
    topSales = [],
    newProducts = [],
    todaysProducts = [],
}) {

    return (
        <UserLayout>
            <style
                dangerouslySetInnerHTML={{
                    __html: `
        @media (min-width: 767px) {
            .detail-box h1 {
                font-size: 3rem !important;
                margin-bottom: 0px;
            }
        }

        @media (min-width: 1199px) {
            .detail-box h1 {
                font-size: 4rem !important;
                margin-bottom: 0px;
            }
        }

        @media (max-width: 570px) {
            .slider_bg_box img {
                width: 100%;
                height: auto;
                aspect-ratio: 16 / 9;
            }

            .detail-box h1 {
                font-size: 4rem !important;
                margin-bottom: 0px;
            }
        }

        @media (max-width: 767px) {
            .slider {
            }
        }

        .body {
            margin: 0;
            font-family: sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .slider {
            position: relative;
            width: 100%;
            height: auto;
            max-height: 400px;
            overflow: hidden;
            background: #fff;
            aspect-ratio: 16/9;
        }

        .slides {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .slide {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transform: scale(0.95);
            visibility: hidden;
            transition: opacity 0.6s linear, transform 0.6s linear;
            display: flex;
            align-items: center;
        }

        .slide.active {
            opacity: 1;
            transform: scale(1);
            visibility: visible;
            z-index: 2;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: unset;
            position: absolute;
            z-index: 0;
            top: 0;
            left: 0;
        }

        .description {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            background-color: #ffffffe8;
            padding: 30px;
            margin-left: 40px;
            opacity: 0;
            transform: translateX(-50px);
            transition: opacity 0.6s linear, transform 0.6s linear;
            backdrop-filter: blur(8px);
            border-radius: 10px;
            overflow: hidden;
        }

        .slide.active .description {
            opacity: 1;
            transform: translateX(0);
        }

        .description h1 {
            margin: 0 0 10px;
            font-size: 28px;
        }

        .description p {
            margin: 0 0 15px;
            font-size: 16px;
        }

        .dots {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 9;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.4);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .dot .active {
            background-color: #000;
        }

        .slide.exit {
            opacity: 0;
            transform: scale(0.95);
            visibility: hidden;
            z-index: 1;
        }
    `,
                }}
            />
            <HeroSlider slides={slides} />

            <Container>
                <DisplayCategory categories={categories} />
                <NewProduct products={newProducts} />
                <TodaysProducts products={todaysProducts} />
                <div className="pb-6">
                    <div className="flex items-center justify-between px-2 py-4">
                        <h2 className="text-xl font-bold">Products</h2>
                        <Link
                            href={route("products.index")}
                            className="px-3 py-2 rounded text-inherit hover:text-indigo-600"
                        >
                            View All
                        </Link>
                    </div>

                    <div className="transition-all duration-300 product_section">
                        <ProductsLoop products={products} />
                    </div>
                </div>
                <TopSales products={topSales} />
            </Container>

            <StaticSlider sliders={ss} />

            <Container>
                <RecommendedProducts products={recommended} />
            </Container>
        </UserLayout>
    );
}
