import { Link, Head } from "@inertiajs/react";
import ApplicationLogo from "../components/ApplicationLogo";

export default function GuestLayout({ children }) {
  return (
    <>
      <Head title={import.meta.env.VITE_APP_NAME || "nolicx"} />

      <div className="font-sans antialiased text-gray-900">
        <div className="flex flex-col items-center justify-center min-h-screen pt-6 bg-gray-100 sm:pt-0">

          {/* Logo */}
          <div>
            <Link href="/">
              <ApplicationLogo className="w-24 h-24 text-gray-500 fill-current" />
            </Link>
          </div>

          {/* Content */}
          <div className="flex justify-center w-full px-6 py-4 mx-auto overflow-hidden sm:rounded-lg">
            {children}
          </div>

        </div>
      </div>
    </>
  );
}
