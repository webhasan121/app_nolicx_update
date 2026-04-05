import { usePage } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import CoinStore from "../../../../livewire/system/store/CoinStore";
import CoastStore from "../../../../livewire/system/store/CoastStore";
import DonationStore from "../../../../livewire/system/store/DonationStore";

export default function Index() {
    const { coinStore = {}, coastStore = {}, donationStore = {} } = usePage().props;

    return (
        <AppLayout
            title="Coin Store"
            header={<PageHeader>Coin Store</PageHeader>}
        >
            <Container>
                <SectionSection>
                    <SectionInner>
                        <div>
                            <CoinStore
                                store={coinStore.store}
                                take={coinStore.take}
                                give={coinStore.give}
                            />
                        </div>
                    </SectionInner>
                </SectionSection>

                <div className="">
                    <SectionSection>
                        <CoastStore store={coastStore.store} />
                    </SectionSection>

                    <SectionSection>
                        <DonationStore store={donationStore.store} />
                    </SectionSection>
                </div>
            </Container>
        </AppLayout>
    );
}
