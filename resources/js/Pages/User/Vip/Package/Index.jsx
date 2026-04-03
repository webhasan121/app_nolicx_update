import { usePage } from "@inertiajs/react";
import VipCart from "../../../../components/VipCart";
import UserDash from "../../../../components/user/dash/UserDash";
import SectionHeader from "../../../../components/dashboard/section/Header";
import Container from "../../../../components/dashboard/Container";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionInner from "../../../../components/dashboard/section/Inner";

export default function VipPackageIndex() {
    const { packages } = usePage().props;

    //   <div>
    //     <x-dashboard.page-header>
    //         VIP Packages
    //     </x-dashboard.page-header>

    //     <x-dashboard.container>
    //         <x-dashboard.section>
    //             <x-dashboard.section.inner>
    //                 <div style="display: grid; grid-template-columns:repeat(auto-fit, 220px); grid-gap:20px; justify-content:center">
    //                     @foreach ($vips as $item)
    //                         <x-vip-cart :$item />
    //                     @endforeach
    //                 </div>
    //             </x-dashboard.section.inner>
    //         </x-dashboard.section>
    //     </x-dashboard.container>

    // </div>

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionInner>
                        <div
                            style={{
                                display: "grid",
                                gridTemplateColumns:
                                    "repeat(auto-fit, minmax(200px, 1fr))",
                                gap: "20px",
                            }}
                        >
                            {packages.map((item) => (
                                <VipCart
                                    key={item.id}
                                    item={item}
                                    type="owner"
                                    active=""
                                />
                            ))}
                        </div>
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
