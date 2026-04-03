import { Link } from "@inertiajs/react";
import { useEffect, useState } from "react";

export default function StaticSlider({ sliders = [] }) {

    const slides = sliders.flatMap(s => s.slides || []);

    const [current, setCurrent] = useState(0);

    useEffect(() => {
        if (!slides.length) return;

        const interval = setInterval(() => {
            setCurrent(prev => (prev + 1) % slides.length);
        }, 5000);

        return () => clearInterval(interval);
    }, [slides.length]);

    if (!slides.length) return null;

    return (
        <div className="body">

            <div className="slider">

                <div className="slides">

                    {slides.map((item, index) => (

                        <div
                            key={item.id ?? index}
                            className={`slide ${index === current ? "active" : ""}`}
                        >

                            <Link
                                href={item.action_url ?? "/products"}
                                className="w-full slide-link"
                            >
                                <img
                                    src={`/storage/${item.image}`}
                                    className="w-full"
                                    alt=""
                                />
                            </Link>

                        </div>

                    ))}

                </div>

            </div>

        </div>
    );
}
