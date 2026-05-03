import { Head, usePage } from "@inertiajs/react";
import { useEffect } from "react";
import Swal from "sweetalert2"; // npm install sweetalert2 kore niben
import SupportButton from "../../components/SupportButton";
import Header from "../../components/user/Header";
import Footer from "../../components/user/Footer";
// Import your components (path gulo apnar file directory onujayi thik kore niben)

export default function UserLayout({ children, title }) {
    // Inertia theke flash message receive korar jonno
    const { flash, appConfig } = usePage().props;

    console.log('appConfig',appConfig);

    // Handle SweetAlert Notifications (Livewire er bodole)
    useEffect(() => {
        if (flash?.success) {
            Swal.fire({
                title: "Congrats !",
                text: flash.success,
                icon: "success",
                confirmButtonText: "OK",
            });
        }
        if (flash?.error) {
            Swal.fire({
                title: "Attention !",
                text: flash.error,
                icon: "error",
                confirmButtonText: "OK",
            });
        }
        if (flash?.warning) {
            Swal.fire({
                title: "Alert !",
                text: flash.warning,
                icon: "warning",
                confirmButtonText: "OK",
            });
        }
        if (flash?.info) {
            Swal.fire({
                title: "Look At!",
                text: flash.info,
                icon: "info",
                confirmButtonText: "OK",
            });
        }
    }, [flash]);

    // Handle Image Zoom
    useEffect(() => {
        if (window.ImageZoom) {
            document
                .querySelectorAll(".product-zoom-container")
                .forEach(function (el) {
                    if (!el.dataset.zoomed) {
                        new window.ImageZoom(el, {
                            width: el.offsetWidth,
                            zoomWidth: 450,
                        });
                        el.dataset.zoomed = true;
                    }
                });
        }
    });

    return (
        <div className="relative">
            <Head>
                <title>{title ? `${title} - Nolicx` : "Nolicx"}</title>

                {/* Google Fonts */}
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link
                    rel="preconnect"
                    href="https://fonts.gstatic.com"
                    crossOrigin="anonymous"
                />
                <link
                    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
                    rel="stylesheet"
                />

                {/* CSS Links (Assuming assets are in the public folder) */}
                {/* <link
                    href="/assets/user/css/font-awesome.min.css"
                    rel="stylesheet"
                /> */}
                <link
                    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
                    rel="stylesheet"
                />
                <link href="/assets/user/css/style.css" rel="stylesheet" />
                <link href="/assets/user/css/responsive.css" rel="stylesheet" />
                <link
                    href="/assets/css/inner-image-zoom.min.css"
                    rel="stylesheet"
                />

                {/* Scripts */}
                <script src="/assets/js/inner-image-zoom.min.js"></script>
                <script src="https://unpkg.com/js-image-zoom@0.4.1/js-image-zoom.js"></script>
            </Head>

            {/* Custom CSS (Blade theke kora) */}
            <style
                dangerouslySetInnerHTML={{
                    __html: `
                body { background-color: #f0f0f0 !important; }
                th { vertical-align: middle !important; font-size: 14px; }
                .discount-badge {
                    position: absolute; top: 0; left: 0; color: white; font-weight: bold;
                    display: inline-flex; align-items: center; justify-content: flex-start;
                    min-width: 72px; width: max-content; height: 32px;
                    padding: 4px 16px 4px 8px; white-space: nowrap;
                    clip-path: polygon(0 0, 100% 0, calc(100% - 18px) 100%, 0 100%);
                    font-size: 16px; line-height: 1;
                }
                .m_body { margin: 0; font-family: sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; }
                .m_slider { position: relative; width: 100%; height: auto; max-height: 400px; overflow: hidden; background: #fff; aspect-ratio: 16/9; }
                .m_slides { width: 100%; height: 100%; position: relative; }
                .m_slide { width: 100%; height: 100%; position: absolute; top: 0; left: 0; opacity: 0; transform: scale(0.95); visibility: hidden; transition: opacity 0.6s linear, transform 0.6s linear; display: flex; align-items: center; }
                .m_slide.m_active { opacity: 1; transform: scale(1); visibility: visible; z-index: 2; }
                .m_slide img { width: 100%; height: 100%; object-fit: unset; position: absolute; z-index: 0; top: 0; left: 0; }
                .m_description { position: relative; z-index: 1; width: 100%; max-width: 400px; background-color: #ffffffe8; padding: 30px; margin-left: 40px; opacity: 0; transform: translateX(-50px); transition: opacity 0.6s linear, transform 0.6s linear; backdrop-filter: blur(8px); border-radius: 10px; overflow: hidden; }
                .m_slide.m_active .m_description { opacity: 1; transform: translateX(0); }
                .m_description h1 { margin: 0 0 10px; font-size: 28px; }
                .m_description p { margin: 0 0 15px; font-size: 16px; }
                .m_dots { position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 9; }
                .m_dot { width: 12px; height: 12px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.4); cursor: pointer; transition: background-color 0.3s; }
                .m_dot .m_active { background-color: #000; }
                .slide.exit { opacity: 0; transform: scale(0.95); visibility: hidden; z-index: 1; }
                .mask_bg { -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent); mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent); }
            `,
                }}
            />

            {/* <SupportButton whatsapp={config('app.system_email')} /> (Jodi component toiri kora thake, ekhane add korben) */}

            <Header />


            <div className="relative">{children}</div>

            <Footer />
            <SupportButton whatsapp={appConfig?.whatsapp_no} />
        </div>
    );
}
